<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('hris:standardize-passwords', function () {
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
