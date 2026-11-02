<?php

namespace App\Filament\Pages;

use App\Services\BackupService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use UnitEnum;

class BackupData extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationLabel = 'Backup Data Sistem';

    protected static ?string $title = 'Cadangan Data & Arsip Sistem';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan & Sistem';

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.backup-data';

    protected BackupService $backupService;

    public function boot(): void
    {
        $this->backupService = app(BackupService::class);
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'super_admin';
    }

    public function getBackupDir(): string
    {
        return app(BackupService::class)->getBackupDir();
    }

    public function getDatabaseStats(): array
    {
        return app(BackupService::class)->getDatabaseStats();
    }

    public function getBackupFiles(): array
    {
        return app(BackupService::class)->getBackupFiles();
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
            ->label('Cadangkan Arsip Lengkap (ZIP: DB + Lampiran + Snapshot)')
            ->icon('heroicon-o-archive-box-arrow-down')
            ->color('success')
            ->size('lg')
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Cadangan Lengkap Sistem')
            ->modalDescription('Sistem akan mengarsipkan database SQLite, seluruh berkas lampiran resmi (foto profil, kuitansi reimbursement, surat sakit, formulir cuti, dokumen SK), serta snapshot data JSON terpadu ke dalam satu file ZIP.')
            ->modalSubmitActionLabel('Ya, Mulai Cadangkan Lengkap')
            ->modalCancelActionLabel('Batal')
            ->action(function () {
                $this->createFullBackup();
            });
    }

    public function deleteBackupAction(): Action
    {
        return Action::make('deleteBackup')
            ->label('Hapus')
            ->color('danger')
            ->size('xs')
            ->icon('heroicon-m-trash')
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Hapus Arsip Cadangan')
            ->modalDescription(fn (array $arguments) => "Apakah Anda yakin ingin menghapus file cadangan '{$arguments['filename']}' secara permanen dari server? Tindakan ini tidak dapat dibatalkan.")
            ->modalSubmitActionLabel('Ya, Hapus Permanen')
            ->modalCancelActionLabel('Batal')
            ->action(function (array $arguments) {
                $this->deleteBackup($arguments['filename']);
            });
    }

    public function createDatabaseBackup(): void
    {
        $backupService = app(BackupService::class);
        $destFilename = $backupService->createDatabaseBackup();

        Notification::make()
            ->title('Cadangan Database Berhasil Dibuat')
            ->body("Berkas cadangan {$destFilename} telah berhasil disimpan dalam direktori aman.")
            ->success()
            ->send();
    }

    public function createFullBackup(): void
    {
        try {
            $creator = auth()->user()?->name . ' (' . auth()->user()?->email . ')';
            $backupService = app(BackupService::class);
            $zipFilename = $backupService->createFullBackup($creator);

            Notification::make()
                ->title('Cadangan Lengkap Berhasil Dibuat')
                ->body("Paket arsip {$zipFilename} (Database + Dokumen Storage + Snapshot JSON) telah siap dan tersimpan dengan aman.")
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Gagal Membuat Cadangan ZIP')
                ->body('Terjadi kendala saat mengompresi file cadangan sistem: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function downloadBackup(string $filename): BinaryFileResponse
    {
        $safeName = basename($filename);
        if ($safeName === '' || $safeName === '.' || $safeName === '..') {
            abort(400, 'Nama berkas tidak valid.');
        }

        $extension = strtolower(pathinfo($safeName, PATHINFO_EXTENSION));
        if (! in_array($extension, ['sqlite', 'zip', 'json', 'sql'])) {
            abort(403, 'Format berkas cadangan tidak diizinkan.');
        }

        $path = $this->getBackupDir() . DIRECTORY_SEPARATOR . $safeName;

        if (! file_exists($path) || ! is_file($path)) {
            abort(404, 'File cadangan tidak ditemukan.');
        }

        return response()->download($path, $safeName);
    }

    public function deleteBackup(string $filename): void
    {
        $backupService = app(BackupService::class);
        $success = $backupService->deleteBackup($filename);

        if ($success) {
            Notification::make()
                ->title('File Cadangan Dihapus')
                ->body("File {$filename} telah berhasil dihapus dari server.")
                ->success()
                ->send();
        }
    }
}
