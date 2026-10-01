<?php

namespace App\Filament\Resources\WarningLetters\Pages;

use App\Filament\Resources\WarningLetters\WarningLetterResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateWarningLetter extends CreateRecord
{
    protected static string $resource = WarningLetterResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Penerbitan Surat Peringatan (SP)')
            ->modalDescription('Penerbitan surat peringatan merupakan sanksi disipliner resmi perbankan. Pastikan jenis SP dan dasar pelanggaran pegawai telah sesuai ketentuan.')
            ->modalSubmitActionLabel('Ya, Terbitkan SP')
            ->modalCancelActionLabel('Batal');
    }
}
