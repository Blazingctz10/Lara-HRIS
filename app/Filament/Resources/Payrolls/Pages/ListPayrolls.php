<?php

namespace App\Filament\Resources\Payrolls\Pages;

use App\Filament\Resources\Payrolls\PayrollResource;
use App\Services\PayrollCalculationService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Grid;

class ListPayrolls extends ListRecords
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_batch')
                ->label('Hitung Otomatis Seluruh Pegawai')
                ->icon('heroicon-o-calculator')
                ->color('success')
                ->visible(fn (): bool => in_array(auth()->user()?->role, ['super_admin', 'hr_admin']))
                ->modalHeading('Kalkulasi Otomatis Gaji Seluruh Pegawai')
                ->modalDescription('Sistem Payroll Engine akan menghitung otomatis Gaji Pokok, Tunjangan Jabatan, Lembur (Approved), Pencairan Reimburse, Denda Telat Presensi, BPJS TK/Kes, dan PPh 21 TER untuk periode yang dipilih.')
                ->modalSubmitActionLabel('Mulai Hitung Otomatis')
                ->form([
                    Grid::make(2)->schema([
                        Select::make('month')
                            ->label('Bulan Periode')
                            ->options([
                                1 => '01 - Januari', 2 => '02 - Februari', 3 => '03 - Maret',
                                4 => '04 - April', 5 => '05 - Mei', 6 => '06 - Juni',
                                7 => '07 - Juli', 8 => '08 - Agustus', 9 => '09 - September',
                                10 => '10 - Oktober', 11 => '11 - November', 12 => '12 - Desember',
                            ])
                            ->default((int) date('m'))
                            ->required(),

                        TextInput::make('year')
                            ->label('Tahun Periode')
                            ->numeric()
                            ->default((int) date('Y'))
                            ->required(),
                    ]),
                ])
                ->action(function (array $data) {
                    $service = app(PayrollCalculationService::class);
                    $result = $service->generateBatchPayroll((int) $data['month'], (int) $data['year']);

                    if ($result['success']) {
                        Notification::make()
                            ->title('Kalkulasi Gaji Selesai')
                            ->body($result['message'])
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Kalkulasi Gaji Gagal')
                            ->body($result['message'])
                            ->danger()
                            ->send();
                    }
                }),

            CreateAction::make()
                ->label('Input Manual Gaji')
                ->visible(fn (): bool => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
        ];
    }
}
