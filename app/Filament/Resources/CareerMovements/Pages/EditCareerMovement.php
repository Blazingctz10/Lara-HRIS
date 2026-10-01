<?php

namespace App\Filament\Resources\CareerMovements\Pages;

use App\Filament\Resources\CareerMovements\CareerMovementResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCareerMovement extends EditRecord
{
    protected static string $resource = CareerMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Hapus Riwayat Karir')
                ->modalDescription('Apakah Anda yakin ingin menghapus arsip SK mutasi/promosi ini?')
                ->modalSubmitActionLabel('Ya, Hapus SK')
                ->modalCancelActionLabel('Batal'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Perubahan SK Karir')
            ->modalDescription('Pastikan nomor SK, cabang tujuan, atau jabatan baru pegawai telah sesuai.')
            ->modalSubmitActionLabel('Ya, Simpan Perubahan')
            ->modalCancelActionLabel('Batal');
    }
}
