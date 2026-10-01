<?php

namespace App\Filament\Resources\Payrolls\Pages;

use App\Filament\Resources\Payrolls\PayrollResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreatePayroll extends CreateRecord
{
    protected static string $resource = PayrollResource::class;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Pembuatan Slip Gaji (Payroll)')
            ->modalDescription('Pastikan komponen gaji pokok, tunjangan kehadiran, lembur, dan potongan pajak/BPJS telah sesuai perhitungan sebelum diterbitkan.')
            ->modalSubmitActionLabel('Ya, Terbitkan Slip Gaji')
            ->modalCancelActionLabel('Batal');
    }
}
