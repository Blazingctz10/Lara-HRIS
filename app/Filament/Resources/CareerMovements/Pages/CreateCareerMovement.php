<?php

namespace App\Filament\Resources\CareerMovements\Pages;

use App\Filament\Resources\CareerMovements\CareerMovementResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateCareerMovement extends CreateRecord
{
    protected static string $resource = CareerMovementResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Penerbitan SK Mutasi / Promosi Karir')
            ->modalDescription('Apakah Anda yakin ingin menerbitkan SK karir ini? Sistem akan secara otomatis memperbarui cabang penempatan atau jabatan resmi pegawai.')
            ->modalSubmitActionLabel('Ya, Terbitkan SK Karir')
            ->modalCancelActionLabel('Batal');
    }
}
