<?php

namespace App\Filament\Resources\WarningLetters\Pages;

use App\Filament\Resources\WarningLetters\WarningLetterResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWarningLetter extends EditRecord
{
    protected static string $resource = WarningLetterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Hapus Surat Peringatan')
                ->modalDescription('Apakah Anda yakin ingin membatalkan dan menghapus surat peringatan ini secara permanen?')
                ->modalSubmitActionLabel('Ya, Hapus SP')
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
            ->modalHeading('Konfirmasi Perubahan Data SP')
            ->modalDescription('Pastikan perubahan nomor SK atau sanksi disipliner telah diverifikasi.')
            ->modalSubmitActionLabel('Ya, Simpan Perubahan')
            ->modalCancelActionLabel('Batal');
    }
}
