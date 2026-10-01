<?php

namespace App\Filament\Resources\JobApplicants\Pages;

use App\Filament\Resources\JobApplicants\JobApplicantResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateJobApplicant extends CreateRecord
{
    protected static string $resource = JobApplicantResource::class;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Simpan Data Pelamar')
            ->modalDescription('Pastikan informasi pelamar kerja dan berkas yang diunggah telah valid.')
            ->modalSubmitActionLabel('Ya, Simpan Pelamar')
            ->modalCancelActionLabel('Batal');
    }
}
