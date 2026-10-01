<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Hapus Data Pegawai')
                ->modalDescription('Perhatian: Data pegawai ini beserta akun pengguna dan data terkait akan dihapus secara permanen dari sistem bank.')
                ->modalSubmitActionLabel('Ya, Hapus Pegawai')
                ->modalCancelActionLabel('Batal'),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Perubahan Data Pegawai')
            ->modalDescription('Pastikan data kepegawaian, jabatan, cabang penempatan, dan status ketenagakerjaan telah diverifikasi sebelum disimpan ke sistem.')
            ->modalSubmitActionLabel('Ya, Simpan Perubahan')
            ->modalCancelActionLabel('Batal');
    }
}
