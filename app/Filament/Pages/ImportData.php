<?php

namespace App\Filament\Pages;

use App\Services\DataMigrationService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class ImportData extends Page
{
    use WithFileUploads;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static ?string $navigationLabel = 'Migrasi & Impor Data';

    protected static ?string $title = 'Pusat Migrasi & Impor Data Lama';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan & Sistem';

    protected static ?int $navigationSort = 98;

    protected string $view = 'filament.pages.import-data';

    public $employeeFile;
    public $attendanceFile;

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'hr_admin']);
    }

    public function importEmployeesAction(): Action
    {
        return Action::make('importEmployees')
            ->label('Mulai Impor & Migrasikan Pegawai')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('primary')
            ->size('lg')
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Migrasi Data Pegawai')
            ->modalDescription('Apakah Anda yakin ingin memulai proses impor data pegawai dari berkas CSV yang diunggah? Akun portal login dan data profil pegawai akan disinkronkan ke dalam sistem.')
            ->modalSubmitActionLabel('Ya, Mulai Impor Pegawai')
            ->modalCancelActionLabel('Batal')
            ->modalIcon('heroicon-o-arrow-up-tray')
            ->action(function () {
                $this->importEmployees();
            });
    }

    public function importAttendancesAction(): Action
    {
        return Action::make('importAttendances')
            ->label('Mulai Impor Log Absensi')
            ->icon('heroicon-o-clock')
            ->color('warning')
            ->size('lg')
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Impor Log Absensi')
            ->modalDescription('Apakah Anda yakin ingin mengimpor riwayat data presensi dari berkas CSV ini ke dalam kalender kehadiran sistem?')
            ->modalSubmitActionLabel('Ya, Mulai Impor Absensi')
            ->modalCancelActionLabel('Batal')
            ->modalIcon('heroicon-o-clock')
            ->action(function () {
                $this->importAttendances();
            });
    }

    /**
     * Unduh template CSV Master Pegawai
     */
    public function downloadEmployeeTemplate(): StreamedResponse
    {
        $service = app(DataMigrationService::class);
        $csv = $service->generateEmployeeTemplateCsv();

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'template_migrasi_pegawai_bpr.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Unduh template CSV Log Absensi
     */
    public function downloadAttendanceTemplate(): StreamedResponse
    {
        $service = app(DataMigrationService::class);
        $csv = $service->generateAttendanceTemplateCsv();

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'template_impor_absensi_bpr.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Eksekusi Impor Pegawai dari file CSV yang diunggah
     */
    public function importEmployees(): void
    {
        $this->validate([
            'employeeFile' => 'required|file|mimes:csv,txt|max:10240',
        ], [
            'employeeFile.required' => 'Silakan pilih berkas CSV data pegawai terlebih dahulu.',
            'employeeFile.mimes' => 'Format berkas harus berupa CSV (.csv) atau TXT.',
            'employeeFile.max' => 'Ukuran berkas maksimal 10 MB.',
        ]);

        try {
            $path = $this->employeeFile->getRealPath();
            $service = app(DataMigrationService::class);
            $result = $service->importEmployeesFromCsv($path);

            if ($result['success']) {
                $errorDetails = ! empty($result['errors']) ? ' (' . count($result['errors']) . ' peringatan)' : '';
                Notification::make()
                    ->title('Migrasi Pegawai Berhasil Selesai')
                    ->body($result['message'] . $errorDetails)
                    ->success()
                    ->persistent()
                    ->send();

                $this->reset('employeeFile');
            } else {
                Notification::make()
                    ->title('Migrasi Pegawai Gagal')
                    ->body($result['message'])
                    ->danger()
                    ->persistent()
                    ->send();
            }
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Terjadi Kesalahan')
                ->body('Gagal memproses file: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Eksekusi Impor Absensi dari file CSV yang diunggah
     */
    public function importAttendances(): void
    {
        $this->validate([
            'attendanceFile' => 'required|file|mimes:csv,txt,xls|max:15360',
        ], [
            'attendanceFile.required' => 'Silakan pilih berkas log absensi terlebih dahulu.',
            'attendanceFile.mimes' => 'Format berkas harus berupa CSV (.csv), TXT, atau Excel (.xls).',
            'attendanceFile.max' => 'Ukuran berkas maksimal 15 MB.',
        ]);

        try {
            $path = $this->attendanceFile->getRealPath();
            $service = app(DataMigrationService::class);
            $result = $service->importAttendancesFromCsv($path);

            if ($result['success']) {
                $errorDetails = ! empty($result['errors']) ? ' (' . count($result['errors']) . ' peringatan)' : '';
                Notification::make()
                    ->title('Impor Log Absensi Berhasil')
                    ->body($result['message'] . $errorDetails)
                    ->success()
                    ->persistent()
                    ->send();

                $this->reset('attendanceFile');
            } else {
                Notification::make()
                    ->title('Impor Log Absensi Gagal')
                    ->body($result['message'])
                    ->danger()
                    ->persistent()
                    ->send();
            }
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Terjadi Kesalahan')
                ->body('Gagal memproses file: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
