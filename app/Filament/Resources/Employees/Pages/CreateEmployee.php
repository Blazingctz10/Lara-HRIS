<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    public function mount(): void
    {
        if (auth()->user()?->role === 'direksi') {
            abort(403, 'Akses ditolak. Akun Direksi hanya memiliki hak pengawasan (Read-Only) data pegawai.');
        }

        parent::mount();
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Pendaftaran Pegawai Baru')
            ->modalDescription('Pastikan data identitas KTP, NIP, kantor penempatan, dan akun masuk pegawai sudah lengkap dan akurat sebelum disimpan.')
            ->modalSubmitActionLabel('Ya, Daftarkan Pegawai')
            ->modalCancelActionLabel('Batal');
    }
}
