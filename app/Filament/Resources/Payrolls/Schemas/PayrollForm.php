<?php

namespace App\Filament\Resources\Payrolls\Schemas;

use App\Models\Employee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PayrollForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Pegawai & Periode Gaji')
                    ->description('Pilih pegawai dan bulan periode penggajian')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('employee_id')
                                ->label('Nama Pegawai')
                                ->options(Employee::where('status', 'Active')->pluck('full_name', 'id'))
                                ->searchable()
                                ->required(),

                            Select::make('period_month')
                                ->label('Bulan Periode')
                                ->options([
                                    1 => '01 - Januari', 2 => '02 - Februari', 3 => '03 - Maret',
                                    4 => '04 - April', 5 => '05 - Mei', 6 => '06 - Juni',
                                    7 => '07 - Juli', 8 => '08 - Agustus', 9 => '09 - September',
                                    10 => '10 - Oktober', 11 => '11 - November', 12 => '12 - Desember',
                                ])
                                ->default((int) date('m'))
                                ->required(),

                            TextInput::make('period_year')
                                ->label('Tahun Periode')
                                ->numeric()
                                ->default((int) date('Y'))
                                ->required(),
                        ]),
                    ]),

                Section::make('Rincian Pendapatan (Earnings)')
                    ->description('Gaji pokok, tunjangan resmi perbankan, lembur dan reimbursement')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('basic_salary')
                                ->label('Gaji Pokok (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->required(),

                            TextInput::make('position_allowance')
                                ->label('Tunjangan Jabatan (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0),

                            TextInput::make('transport_allowance')
                                ->label('Tunjangan Transport (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0),

                            TextInput::make('meal_allowance')
                                ->label('Uang Makan (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0),

                            TextInput::make('overtime_pay')
                                ->label('Uang Lembur (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0),

                            TextInput::make('reimbursement_pay')
                                ->label('Pencairan Reimburse (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0),
                        ]),
                    ]),

                Section::make('Rincian Potongan (Deductions)')
                    ->description('Kewajiban BPJS, Pajak PPh 21, Denda Keterlambatan dan Kasbon')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('bpjs_tk_deduction')
                                ->label('BPJS Ketenagakerjaan (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0),

                            TextInput::make('bpjs_kes_deduction')
                                ->label('BPJS Kesehatan (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0),

                            TextInput::make('pph21_deduction')
                                ->label('Pajak PPh 21 (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0),

                            TextInput::make('late_penalty_deduction')
                                ->label('Denda Keterlambatan (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0),

                            TextInput::make('loan_deduction')
                                ->label('Potongan Kasbon / Pinjaman (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0),
                        ]),
                    ]),

                Section::make('Hasil Akhir & Status Pembayaran')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('net_salary')
                                ->label('Gaji Bersih / Take Home Pay (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->required(),

                            DatePicker::make('payment_date')
                                ->label('Tanggal Pembayaran')
                                ->default(now()),

                            Select::make('status')
                                ->label('Status Slip Gaji')
                                ->options([
                                    'Draft' => 'Draf (Sedang Dihitung)',
                                    'Approved' => 'Disetujui (Siap Rilis)',
                                    'Paid' => 'Dibayarkan / Rilis ke Pegawai',
                                ])
                                ->default('Draft')
                                ->required(),
                        ]),

                        Textarea::make('notes')
                            ->label('Catatan Payroll')
                            ->placeholder('Tambahkan keterangan tambahan jika ada...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
