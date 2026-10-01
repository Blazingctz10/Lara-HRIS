<?php

namespace App\Filament\Pages;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\CareerMovement;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\Position;
use App\Models\User;
use App\Models\WarningLetter;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\File;
use UnitEnum;
use ZipArchive;

class BackupData extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationLabel = 'Backup Data Sistem';

    protected static ?string $title = 'Cadangan Data & Arsip Sistem';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan & Sistem';

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.backup-data';

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'super_admin';
    }

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
                'warning_letters' => WarningLetter::count(),
                'career_movements' => CareerMovement::count(),
                'announcements' => Announcement::count(),
                'branches' => Branch::count(),
                'departments' => Department::count() + Position::count(),
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
                'type' => $isFull ? 'Arsip Lengkap (ZIP: DB + Dokumen)' : 'Database SQLite (.sqlite)',
                'created_at' => date('d M Y, H:i:s', $file->getMTime()),
                'timestamp' => $file->getMTime(),
            ];
        }

        // Urutkan dari yang paling baru
        usort($result, fn ($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $result;
    }

    public function createDatabaseBackupAction(): Action
    {
        return Action::make('createDatabaseBackup')
            ->label('Cadangkan Database Saja')
            ->icon('heroicon-o-circle-stack')
            ->color('primary')
            ->size('lg')
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Cadangan Database')
            ->modalDescription('Sistem akan membuat salinan basis data aktif Core HRIS PT BPR Banda Raya ke direktori arsip cadangan aman.')
            ->modalSubmitActionLabel('Ya, Mulai Cadangkan')
            ->modalCancelActionLabel('Batal')
            ->action(function () {
                $this->createDatabaseBackup();
            });
    }

    public function createFullBackupAction(): Action
    {
        return Action::make('createFullBackup')
            ->label('Cadangkan Arsip Lengkap (ZIP: DB + Lampiran)')
            ->icon('heroicon-o-archive-box-arrow-down')
            ->color('success')
            ->size('lg')
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Cadangan Lengkap Sistem')
            ->modalDescription('Sistem akan mengarsipkan database dan seluruh berkas lampiran resmi (foto profil, surat sakit, dokumen cuti) ke dalam satu file ZIP terpadu. Proses ini aman dan tidak mengganggu operasional sistem.')
            ->modalSubmitActionLabel('Ya, Mulai Cadangkan Lengkap')
            ->modalCancelActionLabel('Batal')
            ->action(function () {
                $this->createFullBackup();
            });
    }

    public function createDatabaseBackup(): void
    {
        $dir = $this->getBackupDir();
        $timestamp = date('Y-m-d_His');
        $destFilename = "backup_hris_db_{$timestamp}.sqlite";
        $destPath = $dir . DIRECTORY_SEPARATOR . $destFilename;

        $sourceDb = database_path('database.sqlite');
        if (File::exists($sourceDb)) {
            File::copy($sourceDb, $destPath);
        } else {
            // Jika bukan file sqlite, buat file data ringkasan cadangan JSON
            $exportData = [
                'generated_at' => now()->toIso8601String(),
                'version' => '1.0.0',
                'system' => 'Core HRIS PT BPR Banda Raya',
                'users' => User::all(),
                'employees' => Employee::all(),
                'branches' => Branch::all(),
                'attendances' => Attendance::all(),
                'leave_requests' => LeaveRequest::all(),
            ];
            File::put($dir . DIRECTORY_SEPARATOR . "backup_hris_data_{$timestamp}.json", json_encode($exportData, JSON_PRETTY_PRINT));
            $destFilename = "backup_hris_data_{$timestamp}.json";
        }

        Notification::make()
            ->title('Cadangan Database Berhasil Dibuat')
            ->body("Berkas cadangan {$destFilename} telah berhasil disimpan dalam direktori aman.")
            ->success()
            ->send();
    }

    public function createFullBackup(): void
    {
        $dir = $this->getBackupDir();
        $timestamp = date('Y-m-d_His');
        $zipFilename = "backup_hris_full_{$timestamp}.zip";
        $zipPath = $dir . DIRECTORY_SEPARATOR . $zipFilename;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            // 1. Masukkan file database sqlite
            $sourceDb = database_path('database.sqlite');
            if (File::exists($sourceDb)) {
                $zip->addFile($sourceDb, 'database/database.sqlite');
            }

            // 2. Masukkan file JSON metadata
            $metadata = [
                'backup_type' => 'Full System Backup',
                'created_at' => now()->toIso8601String(),
                'system' => 'Core HRIS PT BPR Banda Raya',
                'app_env' => config('app.env'),
                'super_admin_creator' => auth()->user()?->name . ' (' . auth()->user()?->email . ')',
                'stats' => $this->getDatabaseStats()['counts'],
            ];
            $zip->addFromString('backup_manifest.json', json_encode($metadata, JSON_PRETTY_PRINT));

            // 3. Masukkan direktori public storage jika ada
            $storagePublic = storage_path('app/public');
            if (File::isDirectory($storagePublic)) {
                $files = File::allFiles($storagePublic);
                foreach ($files as $file) {
                    $relative = 'storage/' . $file->getRelativePathname();
                    $zip->addFile($file->getPathname(), $relative);
                }
            }

            $zip->close();

            Notification::make()
                ->title('Cadangan Lengkap Berhasil Dibuat')
                ->body("Paket arsip {$zipFilename} telah siap dan tersimpan dengan aman.")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Gagal Membuat Cadangan ZIP')
                ->body('Terjadi kendala saat mengompresi file cadangan sistem.')
                ->danger()
                ->send();
        }
    }

    public function downloadBackup(string $filename): BinaryFileResponse
    {
        $safeName = basename($filename);
        $path = $this->getBackupDir() . DIRECTORY_SEPARATOR . $safeName;

        if (! File::exists($path)) {
            abort(404, 'File cadangan tidak ditemukan.');
        }

        return response()->download($path, $safeName);
    }

    public function deleteBackup(string $filename): void
    {
        $safeName = basename($filename);
        $path = $this->getBackupDir() . DIRECTORY_SEPARATOR . $safeName;

        if (File::exists($path)) {
            File::delete($path);

            Notification::make()
                ->title('File Cadangan Dihapus')
                ->body("File {$safeName} telah berhasil dihapus dari server.")
                ->success()
                ->send();
        }
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
