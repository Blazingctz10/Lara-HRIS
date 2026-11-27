<?php

namespace App\Filament\Resources\AttendanceDispensations\Pages;

use App\Filament\Resources\AttendanceDispensations\AttendanceDispensationResource;
use App\Services\DataMigrationService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ListAttendanceDispensations extends ListRecords
{
    protected static string $resource = AttendanceDispensationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import_legacy_logbook')
                ->label('Impor Logbook Lama (.xlsx / .csv)')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin']))
                ->modalHeading('Impor & Selaraskan Logbook Absen Lama')
                ->modalDescription('Unggah berkas Matriks Absen Lama (seperti Rekap Absen.xlsx) atau tabel logbook (.xlsx / .csv). Sistem otomatis mengekstrak kode MC/Sakit, Izin, TA, Izin Telat, dan Cuti, menyelaraskan presensi, serta melewati pegawai resign.')
                ->form([
                    FileUpload::make('logbook_file')
                        ->label('Pilih Berkas Logbook (.xlsx, .xls, atau .csv)')
                        ->acceptedFileTypes([
                            'text/csv',
                            'text/plain',
                            'application/vnd.ms-excel',
                            'application/x-msexcel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/octet-stream',
                        ])
                        ->rules([
                            fn () => function (string $attribute, $value, \Closure $fail) {
                                if ($value && method_exists($value, 'getClientOriginalExtension')) {
                                    $ext = strtolower($value->getClientOriginalExtension());
                                    if (! in_array($ext, ['xls', 'xlsx', 'csv', 'txt'], true)) {
                                        $fail('Format berkas harus berupa Excel (.xlsx, .xls), CSV (.csv), atau TXT.');
                                    }
                                }
                            },
                        ])
                        ->maxSize(20480)
                        ->disk('local')
                        ->directory('temp_imports')
                        ->required(),
                ])
                ->modalSubmitActionLabel('Impor & Selaraskan')
                ->action(function (array $data) {
                    $path = Storage::disk('local')->path($data['logbook_file']);
                    $result = app(DataMigrationService::class)->importDispensationsFromFile($path, auth()->id());

                    if ($result['success']) {
                        Notification::make()
                            ->title('Logbook Berhasil Diselaraskan')
                            ->body($result['message'])
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Gagal Mengimpor Logbook')
                            ->body($result['message'])
                            ->danger()
                            ->send();
                    }

                    if (File::exists($path)) {
                        File::delete($path);
                    }
                }),

            CreateAction::make()
                ->label('Catat Dispensasi Baru (HR/GA)')
                ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin', 'branch_manager'])),
        ];
    }
}
