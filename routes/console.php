<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('hris:standardize-passwords', function () {
    if (app()->isProduction()) {
        $this->error('[SEC001] Perintah ini DILARANG dijalankan di lingkungan PRODUCTION demi keamanan data perbankan.');
        return 1;
    }

    $this->info('Memulai standarisasi seluruh kata sandi akun sistem BPR Banda Raya...');

    $defaultPassword = \Illuminate\Support\Facades\Hash::make('password');
    $count = 0;

    // 1. Reset semua password User yang ada menjadi 'password'
    foreach (\App\Models\User::all() as $user) {
        $user->update(['password' => $defaultPassword]);
        $count++;
    }

    // 2. Pastikan akun Direksi tersedia
    $hoBranch = \App\Models\Branch::where('type', 'Head Office')->first() ?? \App\Models\Branch::first();
    $direksiUser = \App\Models\User::firstOrCreate(
        ['email' => 'direksi@bprbandaraya.com'],
        [
            'name' => 'Direktur Utama BPR Banda Raya',
            'password' => $defaultPassword,
            'role' => 'direksi',
            'branch_id' => $hoBranch?->id,
        ]
    );

    if ($direksiUser->role !== 'direksi') {
        $direksiUser->update(['role' => 'direksi', 'password' => $defaultPassword]);
    }

    $direksiEmp = \App\Models\Employee::where('email', 'direksi@bprbandaraya.com')->first();
    if (! $direksiEmp) {
        \App\Models\Employee::create([
            'user_id' => $direksiUser->id,
            'nip' => 'BPR-DIR-0001',
            'nik' => '2171010101700001',
            'full_name' => 'Direktur Utama BPR Banda Raya',
            'email' => 'direksi@bprbandaraya.com',
            'phone' => '081177000001',
            'gender' => 'Male',
            'birth_date' => '1970-01-01',
            'address' => 'Komplek Pertokoan Nagoya Hill Blok G No. 1, Batam',
            'branch_id' => $hoBranch?->id,
            'department_id' => \App\Models\Department::first()?->id ?? 1,
            'position_id' => \App\Models\Position::first()?->id ?? 1,
            'employment_status' => 'Permanent',
            'join_date' => '2010-01-01',
            'status' => 'Active',
            'bank_account_number' => '1200000000',
            'bank_account_holder' => 'Direktur Utama BPR',
            'npwp' => '80.000.000.0-011.000',
        ]);
    }

    // 3. Pastikan setiap pegawai memiliki user login
    foreach (\App\Models\Employee::all() as $emp) {
        if (! $emp->user_id && $emp->email) {
            $user = \App\Models\User::firstOrCreate(
                ['email' => $emp->email],
                [
                    'name' => $emp->full_name,
                    'password' => $defaultPassword,
                    'role' => 'employee',
                    'branch_id' => $emp->branch_id,
                ]
            );
            $emp->updateQuietly(['user_id' => $user->id]);
        }
    }

    $this->info("Berhasil! {$count} akun pengguna telah disinkronkan dan direset ke kata sandi: 'password'.");
    $this->table(
        ['Nama Akun', 'Alamat Email', 'Role Sistem', 'Akses Panel', 'Kata Sandi'],
        \App\Models\User::all(['name', 'email', 'role'])->map(function ($u) {
            return [
                $u->name,
                $u->email,
                $u->role,
                $u->role === 'employee' ? '/portal (ESS)' : '/admin & /portal',
                'password',
            ];
        })
    );
})->purpose('Standarisasi seluruh akun pengguna ke kata sandi default: password');

Artisan::command('hris:backup {--type=full : Tipe arsip cadangan (full|db)}', function (\App\Services\BackupService $backupService) {
    $type = $this->option('type') ?: 'full';
    $this->info("Memulai pencadangan sistem Core HRIS PT BPR Banda Raya (Mode: {$type})...");

    try {
        if ($type === 'db') {
            $filename = $backupService->createDatabaseBackup();
            $this->info("✓ Cadangan basis data berhasil diamankan: {$filename}");
        } else {
            $filename = $backupService->createFullBackup('Sistem Terjadwal (Perintah CLI)');
            $this->info("✓ Cadangan arsip lengkap (DB + Dokumen + Snapshot JSON) berhasil dibuat: {$filename}");
        }
    } catch (\Throwable $e) {
        $this->error("Gagal melakukan pencadangan: {$e->getMessage()}");
        return 1;
    }

    return 0;
})->purpose('Pencadangan otomatis database dan lampiran dokumen resmi HRIS');

\Illuminate\Support\Facades\Schedule::call(function (\App\Services\BackupService $backupService) {
    $settings = $backupService->getSettings();
    if (! ($settings['auto_backup_enabled'] ?? false)) {
        return;
    }

    $currentTime = now()->format('H:i');
    $scheduledTime = $settings['schedule_time'] ?? '02:00';

    if ($currentTime !== $scheduledTime) {
        return;
    }

    $frequency = $settings['frequency'] ?? 'daily';
    if ($frequency === 'weekdays' && now()->isSunday()) {
        return;
    }
    if ($frequency === 'weekly' && now()->dayOfWeek !== 0) {
        return;
    }

    $backupService->executeScheduledBackup("Scheduler Otomatis ({$scheduledTime} WIB)");
})->everyMinute()
  ->name('hris:auto-backup-runner')
  ->withoutOverlapping()
  ->appendOutputTo(storage_path('logs/backup.log'));

// Failsafe Pembersihan & Pembatasan Ukuran Log Otomatis (Setiap Hari Pukul 02:30 WIB)
\Illuminate\Support\Facades\Schedule::call(function () {
    // 1. Bersihkan audit login_logs yang lebih tua dari 180 hari (6 bulan)
    \App\Models\LoginLog::where('login_at', '<', now()->subDays(180))->delete();

    // 2. Failsafe ukuran file log: jika ada file log > 50 MB, pangkas agar menyisakan 2 MB terakhir
    $logDir = storage_path('logs');
    if (is_dir($logDir)) {
        $maxBytes = 50 * 1024 * 1024; // 50 MB
        $keepBytes = 2 * 1024 * 1024; // 2 MB
        foreach (glob($logDir . DIRECTORY_SEPARATOR . '*.log') ?: [] as $filePath) {
            if (! is_file($filePath) || ! is_readable($filePath) || ! is_writable($filePath)) {
                continue;
            }
            $size = @filesize($filePath);
            if ($size !== false && $size > $maxBytes) {
                $handle = @fopen($filePath, 'rb');
                if ($handle) {
                    @fseek($handle, -$keepBytes, SEEK_END);
                    $tail = @fread($handle, $keepBytes);
                    @fclose($handle);
                    if ($tail !== false) {
                        @file_put_contents($filePath, "[AUTO-TRIMMED: Log melebihi 50MB untuk menjaga ruang disk server]\n" . $tail, LOCK_EX);
                    }
                }
            }
        }
    }
})->dailyAt('02:30')
  ->name('hris:log-failsafe-prune')
  ->withoutOverlapping();

