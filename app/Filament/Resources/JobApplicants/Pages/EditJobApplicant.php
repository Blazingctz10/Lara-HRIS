<?php

namespace App\Filament\Resources\JobApplicants\Pages;

use App\Filament\Resources\JobApplicants\JobApplicantResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJobApplicant extends EditRecord
{
    protected static string $resource = JobApplicantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Hapus Data Pelamar')
                ->modalDescription('Apakah Anda yakin ingin menghapus data pelamar ini?'),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Perubahan Data Pelamar')
            ->modalDescription('Simpan pembaruan status dan catatan evaluasi rekrutmen ini?')
            ->modalSubmitActionLabel('Ya, Simpan Perubahan');
    }
}
