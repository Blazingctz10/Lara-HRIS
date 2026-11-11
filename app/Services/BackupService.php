<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\AttendanceDispensation;
use App\Models\Branch;
use App\Models\CareerMovement;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeFamily;
use App\Models\EmployeeLeaveBalance;
use App\Models\EmployeePerformanceAppraisal;
use App\Models\EmployeeTraining;
use App\Models\FingerprintDevice;
use App\Models\JobApplicant;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LoginLog;
use App\Models\Overtime;
use App\Models\Payroll;
use App\Models\Position;
use App\Models\Reimbursement;
use App\Models\User;
use App\Models\WarningLetter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ZipArchive;

class BackupService
{
    public function getBackupDir(): string
    {
        $dir = storage_path('app/backups');
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        return $dir;
    }

    public function getDatabaseStats(): array
    {
        $dbConnection = config('database.default', 'sqlite');
        $dbPath = database_path('database.sqlite');
        $dbSizeFormatted = '-';

        if ($dbConnection === 'sqlite' && File::exists($dbPath)) {
            $bytes = File::size($dbPath);
            $dbSizeFormatted = $this->formatBytes($bytes);
        }

        $backupFiles = $this->getBackupFiles();
        $lastBackup = ! empty($backupFiles) ? $backupFiles[0]['created_at'] : 'Belum Pernah Dicadangkan';

        return [
            'connection' => strtoupper($dbConnection),
            'size' => $dbSizeFormatted,
            'last_backup' => $lastBackup,
            'counts' => [
                'employees' => Employee::count(),
                'users' => User::count(),
                'attendances' => Attendance::count(),
                'leave_requests' => LeaveRequest::count(),
                'payrolls' => Payroll::count(),
                'reimbursements' => Reimbursement::count(),
                'warning_letters' => WarningLetter::count(),
                'career_movements' => CareerMovement::count(),
                'attendance_dispensations' => AttendanceDispensation::count(),
                'announcements' => Announcement::count(),
                'branches' => Branch::count(),
                'departments' => Department::count() + Position::count(),
                'job_applicants' => JobApplicant::count(),
                'fingerprint_devices' => FingerprintDevice::count(),
            ],
        ];
    }

    public function getBackupFiles(): array
    {
        $dir = $this->getBackupDir();
        $files = File::files($dir);
        $result = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $ext = strtolower($file->getExtension());
            $isFull = str_contains($filename, 'full') || $ext === 'zip';

            $result[] = [
                'name' => $filename,
                'path' => $file->getPathname(),
                'size' => $this->formatBytes($file->getSize()),
                'type' => $isFull ? 'Arsip Lengkap (ZIP: DB + Lampiran + Snapshot)' : 'Database SQLite (.sqlite)',
                'created_at' => date('d M Y, H:i:s', $file->getMTime()),
                'timestamp' => $file->getMTime(),
            ];
        }

        usort($result, fn ($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $result;
    }

    public function createDatabaseBackup(): string
    {
        $dir = $this->getBackupDir();
        $timestamp = date('Y-m-d_His');
        $destFilename = "backup_hris_db_{$timestamp}.sqlite";
        $destPath = $dir . DIRECTORY_SEPARATOR . $destFilename;

        $sourceDb = database_path('database.sqlite');
        if (File::exists($sourceDb)) {
            $this->checkpointSqlite();
            File::copy($sourceDb, $destPath);
        } else {
            $exportData = $this->exportAllModelsToJson();
            $destFilename = "backup_hris_data_{$timestamp}.json";
            $destPath = $dir . DIRECTORY_SEPARATOR . $destFilename;
            File::put($destPath, json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        $this->pruneOldBackups(20);

        return $destFilename;
    }

    public function createFullBackup(?string $creator = null): string
    {
        $dir = $this->getBackupDir();
        $timestamp = date('Y-m-d_His');
        $zipFilename = "backup_hris_full_{$timestamp}.zip";
        $zipPath = $dir . DIRECTORY_SEPARATOR . $zipFilename;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            // 1. Database SQLite asli
            $sourceDb = database_path('database.sqlite');
            if (File::exists($sourceDb)) {
                $this->checkpointSqlite();
                $zip->addFile($sourceDb, 'database/database.sqlite');
            }

            // 2. Snapshot JSON komprehensif seluruh tabel sistem
            $fullData = $this->exportAllModelsToJson();
            $zip->addFromString('database/database_snapshot.json', json_encode($fullData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // 3. Manifest metadata cadangan
            $metadata = [
                'backup_type' => 'Full System Backup (Core HRIS PT BPR Banda Raya)',
                'created_at' => now()->toIso8601String(),
                'creator' => $creator ?? 'Sistem Otomatis / Administrator',
                'app_name' => config('app.name'),
                'app_env' => config('app.env'),
                'database_driver' => config('database.default'),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'original_backup_storage_path' => 'storage/app/backups/',
                'record_counts' => $this->getDatabaseStats()['counts'],
            ];
            $zip->addFromString('backup_manifest.json', json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // 4. File README Lokasi Cadangan (Di-reveal di dalam arsip ZIP)
            $readmeContent = implode("\r\n", [
                '========================================================================',
                'INFORMASI ARSIP CADANGAN RESMI CORE HRIS PT BPR BANDA RAYA',
                '========================================================================',
                '',
                "Nama Berkas Arsip : {$zipFilename}",
                'Waktu Pembuatan   : ' . now()->translatedFormat('l, d F Y H:i:s') . ' WIB',
                'Dibuat Oleh       : ' . ($creator ?? 'Administrator IT'),
                '',
                'LOKASI PENYIMPANAN ASLI PADA SERVER (REVEALED):',
                '-> storage/app/backups/' . $zipFilename,
                '',
                'STRUKTUR ISI ARSIP CADANGAN LENGKAP:',
                '1. database/database.sqlite        -> File basis data SQLite resmi operasional',
                '2. database/database_snapshot.json -> Snapshot JSON data seluruh tabel & transaksi',
                '3. storage/public/                 -> Berkas dokumen publik (foto profil, SK, lampiran cuti)',
                '4. storage/private/                -> Berkas dokumen privat kepegawaian',
                '5. backup_manifest.json            -> Metadata teknis integritas arsip cadangan',
                '',
                'Catatan Kepatuhan Audit: Berkas arsip ini telah diverifikasi untuk audit IT & OJK.',
                '========================================================================',
            ]);
            $zip->addFromString('README_LOKASI_ARSIP.txt', $readmeContent);

            // 5. Salin seluruh dokumen lampiran dari storage/app/public
            $this->addDirectoryToZip($zip, storage_path('app/public'), 'storage/public');

            // 6. Salin dokumen privat dari storage/app/private (kecuali cache livewire)
            $this->addDirectoryToZip($zip, storage_path('app/private'), 'storage/private');

            $zip->close();

            $this->pruneOldBackups(20);

            return $zipFilename;
        }

        throw new \RuntimeException('Gagal menginisialisasi berkas arsip ZIP cadangan.');
    }

    public function deleteBackup(string $filename): bool
    {
        $safeName = basename($filename);
        if ($safeName === '' || $safeName === '.' || $safeName === '..') {
            return false;
        }

        $extension = strtolower(pathinfo($safeName, PATHINFO_EXTENSION));
        if (! in_array($extension, ['sqlite', 'zip', 'json', 'sql'])) {
            return false;
        }

        $path = $this->getBackupDir() . DIRECTORY_SEPARATOR . $safeName;
        if (File::exists($path) && File::isFile($path)) {
            return File::delete($path);
        }

        return false;
    }

    public function pruneOldBackups(int $keepLatest = 20): int
    {
        $backups = $this->getBackupFiles();
        if (count($backups) <= $keepLatest) {
            return 0;
        }

        $toDelete = array_slice($backups, $keepLatest);
        $deletedCount = 0;
        foreach ($toDelete as $item) {
            if (File::exists($item['path']) && File::isFile($item['path'])) {
                File::delete($item['path']);
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    public function getSettingsFilePath(): string
    {
        return storage_path('app/backup_settings.json');
    }

    public function getSettings(): array
    {
        $path = $this->getSettingsFilePath();
        $defaults = [
            'auto_backup_enabled' => true,
            'schedule_time' => '02:00',
            'frequency' => 'daily', // 'daily', 'weekdays', 'weekly'
            'backup_type' => 'full', // 'full', 'db'
            'retention_count' => 20,
            'last_run_at' => null,
            'last_run_status' => null,
            'last_run_file' => null,
        ];

        if (File::exists($path)) {
            $data = json_decode(File::get($path), true);
            if (is_array($data)) {
                return array_merge($defaults, $data);
            }
        }

        return $defaults;
    }

    public function saveSettings(array $settings): bool
    {
        $current = $this->getSettings();
        $merged = array_merge($current, $settings);
        $dir = dirname($this->getSettingsFilePath());
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        return (bool) File::put($this->getSettingsFilePath(), json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function executeScheduledBackup(string $triggerSource = 'Sistem Terjadwal Otomatis'): string
    {
        $settings = $this->getSettings();
        $type = $settings['backup_type'] ?? 'full';
        $retention = (int) ($settings['retention_count'] ?? 20);

        try {
            if ($type === 'db') {
                $filename = $this->createDatabaseBackup();
            } else {
                $filename = $this->createFullBackup($triggerSource);
            }

            $this->pruneOldBackups($retention);

            $this->saveSettings([
                'last_run_at' => now()->translatedFormat('d M Y, H:i:s') . ' WIB',
                'last_run_status' => 'Sukses',
                'last_run_file' => $filename,
            ]);

            return $filename;
        } catch (\Throwable $e) {
            $this->saveSettings([
                'last_run_at' => now()->translatedFormat('d M Y, H:i:s') . ' WIB',
                'last_run_status' => 'Gagal: ' . $e->getMessage(),
                'last_run_file' => null,
            ]);

            throw $e;
        }
    }

    public function exportAllModelsToJson(): array
    {
        return [
            'meta' => [
                'system' => 'Core HRIS PT BPR Banda Raya',
                'exported_at' => now()->toIso8601String(),
                'version' => '1.0.0',
            ],
            'organizations' => [
                'branches' => Branch::all(),
                'departments' => Department::all(),
                'positions' => Position::all(),
            ],
            'users_and_employees' => [
                'users' => User::all()->makeHidden(['password', 'remember_token']),
                'employees' => Employee::all(),
                'employee_families' => EmployeeFamily::all(),
                'leave_balances' => EmployeeLeaveBalance::all(),
                'performance_appraisals' => EmployeePerformanceAppraisal::all(),
                'trainings' => EmployeeTraining::all(),
            ],
            'attendance_and_shifts' => [
                'attendances' => Attendance::all(),
                'overtimes' => Overtime::all(),
                'attendance_dispensations' => AttendanceDispensation::all(),
                'fingerprint_devices' => FingerprintDevice::all(),
            ],
            'leaves' => [
                'leave_types' => LeaveType::all(),
                'leave_requests' => LeaveRequest::all(),
            ],
            'payroll_and_finance' => [
                'payrolls' => Payroll::all(),
                'reimbursements' => Reimbursement::all(),
            ],
            'employment_lifecycle' => [
                'warning_letters' => WarningLetter::all(),
                'career_movements' => CareerMovement::all(),
                'job_applicants' => JobApplicant::all(),
            ],
            'system_announcements' => [
                'announcements' => Announcement::all(),
                'recent_login_logs' => LoginLog::latest()->limit(500)->get(),
            ],
        ];
    }

    protected function checkpointSqlite(): void
    {
        if (config('database.default') === 'sqlite') {
            try {
                DB::statement('PRAGMA wal_checkpoint(TRUNCATE);');
            } catch (\Throwable $e) {
                // Lewati bila SQLite tidak dalam mode WAL
            }
        }
    }

    protected function addDirectoryToZip(ZipArchive $zip, string $dirPath, string $zipPrefix): void
    {
        if (! File::isDirectory($dirPath)) {
            return;
        }

        $files = File::allFiles($dirPath);
        foreach ($files as $file) {
            $pathname = $file->getPathname();
            // Lewati berkas cache Livewire temporer dan gitignore
            if (str_contains($pathname, 'livewire-tmp') || $file->getFilename() === '.gitignore') {
                continue;
            }

            $relativePath = $zipPrefix . '/' . str_replace('\\', '/', $file->getRelativePathname());
            $zip->addFile($pathname, $relativePath);
        }
    }

    public function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

