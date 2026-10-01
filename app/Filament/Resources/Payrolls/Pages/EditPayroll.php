<?php

namespace App\Filament\Resources\Payrolls\Pages;

use App\Filament\Resources\Payrolls\PayrollResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPayroll extends EditRecord
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Hapus Data Slip Gaji')
                ->modalDescription('Apakah Anda yakin ingin menghapus data perhitungan payroll ini?')
                ->modalSubmitActionLabel('Ya, Hapus Payroll')
                ->modalCancelActionLabel('Batal'),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Penyesuaian Data Payroll')
            ->modalDescription('Pastikan penyesuaian nominal gaji atau tunjangan telah disetujui.')
            ->modalSubmitActionLabel('Ya, Simpan Perubahan')
            ->modalCancelActionLabel('Batal');
    }
}
