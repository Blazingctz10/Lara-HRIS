<?php

namespace App\Filament\Resources\Payrolls\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PayrollForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Slip Gaji Karyawan')
                    ->description('Pencatatan arsip nominal gaji manual per periode')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('employee_id')
                                ->relationship('employee', 'full_name')
                                ->label('Nama Pegawai')
                                ->searchable()
                                ->preload()
                                ->required(),

                            Select::make('period_month')
                                ->label('Bulan Periode')
                                ->options([
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                                ])
                                ->default(now()->month)
                                ->required(),

                            Select::make('period_year')
                                ->label('Tahun Periode')
                                ->options([
                                    2025 => '2025',
                                    2026 => '2026',
                                    2027 => '2027',
                                ])
                                ->default(now()->year)
                                ->required(),
                        ]),

                        Grid::make(4)->schema([
                            TextInput::make('basic_salary')
                                ->label('Gaji Pokok')
                                ->numeric()
                                ->prefix('Rp')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateNet($get, $set)),

                            TextInput::make('allowances')
                                ->label('Total Tunjangan')
                                ->helperText('Jabatan, Transport, Cash Handling Teller')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateNet($get, $set)),

                            TextInput::make('deductions')
                                ->label('Total Potongan')
                                ->helperText('BPJS Ketenagakerjaan / Kesehatan, Pinjaman')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateNet($get, $set)),

                            TextInput::make('net_salary')
                                ->label('Gaji Bersih (Take Home Pay)')
                                ->numeric()
                                ->prefix('Rp')
                                ->readOnly()
                                ->required(),
                        ]),

                        Grid::make(2)->schema([
                            DatePicker::make('payment_date')
                                ->label('Tanggal Pembayaran / Transfer')
                                ->default(now())
                                ->native(false),

                            Select::make('status')
                                ->label('Status Slip')
                                ->options([
                                    'Draft' => 'Draft (Penyusunan)',
                                    'Approved' => 'Approved (Disetujui Pimpinan)',
                                    'Paid' => 'Paid (Telah Ditransfer)',
                                ])
                                ->default('Draft')
                                ->required(),
                        ]),

                        Textarea::make('notes')
                            ->label('Catatan Slip Gaji')
                            ->rows(2),
                    ]),
            ]);
    }

    private static function calculateNet(Get $get, Set $set): void
    {
        $basic = (float) ($get('basic_salary') ?? 0);
        $allowance = (float) ($get('allowances') ?? 0);
        $deduction = (float) ($get('deductions') ?? 0);

        $net = max(0, $basic + $allowance - $deduction);
        $set('net_salary', $net);
    }
}
