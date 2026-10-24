<?php

namespace App\Filament\Portal\Resources\Reimbursements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReimbursementForm
{
    public static function configure(Schema $schema): Schema
    {
        $employee = auth()->user()?->employee;

        return $schema
            ->components([
                Section::make('Formulir Pengajuan Klaim Reimbursement')
                    ->description('Klaim biaya bensin operasional AO, survei lapangan, atau tunjangan medis')
                    ->schema([
                        Hidden::make('employee_id')
                            ->default($employee?->id),

                        Hidden::make('status')
                            ->default('Pending'),

                        Grid::make(2)->schema([
                            Select::make('category')
                                ->label('Kategori Klaim')
                                ->options([
                                    'Bensin & Transport AO' => 'Bensin & Transport AO',
                                    'Survei Calon Debitur' => 'Survei Lapangan Calon Debitur',
                                    'Kesehatan / Kacamata' => 'Tunjangan Medis / Kacamata',
                                    'Konsumsi Lembur' => 'Konsumsi Lembur Closing',
                                    'Operasional Kas Cabang' => 'Operasional Kas Cabang',
                                ])
                                ->default('Bensin & Transport AO')
                                ->required(),

                            TextInput::make('title')
                                ->label('Judul / Keperluan Klaim')
                                ->placeholder('Contoh: Bensin Kunjungan Debitur UMKM Nagoya')
                                ->required(),

                            DatePicker::make('date')
                                ->label('Tanggal Pengeluaran')
                                ->default(now())
                                ->required(),

                            TextInput::make('amount')
                                ->label('Total Biaya yang Diajukan (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->placeholder('Contoh: 150000')
                                ->required(),
                        ]),

                        Textarea::make('description')
                            ->label('Uraian Keterangan Pengeluaran')
                            ->placeholder('Jelaskan rincian keperluan, nomor plat kendaraan, atau nama debitur')
                            ->rows(3),

                        FileUpload::make('receipt_photo')
                            ->label('Upload Foto Kwitansi / Struk SPBU / Nota Pembayaran')
                            ->disk('public')
                            ->directory('reimbursements/receipts')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/jpg'])
                            ->imageEditor()
                            ->maxSize(5120)
                            ->required(),
                    ]),
            ]);
    }
}
