<?php

namespace App\Filament\Resources\Reimbursements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
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
        return $schema
            ->components([
                Section::make('Formulir Klaim Reimbursement & Operasional Lapangan')
                    ->description('Pencatatan klaim pengeluaran dinas / medis / bensin survei debitur')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('employee_id')
                                ->relationship('employee', 'full_name')
                                ->label('Nama Pegawai / AO')
                                ->searchable()
                                ->preload()
                                ->required(),

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
                                ->label('Judul Klaim')
                                ->placeholder('Contoh: Bensin Survei Debitur UMKM Bengkong')
                                ->required(),

                            DatePicker::make('date')
                                ->label('Tanggal Pengeluaran')
                                ->default(now())
                                ->required(),

                            TextInput::make('amount')
                                ->label('Nominal Pengeluaran (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->required(),

                            Select::make('status')
                                ->label('Status Approval & Pembayaran')
                                ->options([
                                    'Pending' => 'Menunggu Approval Pincab',
                                    'Approved_Pincab' => 'Disetujui Pincab (Verifikasi Kasir)',
                                    'Paid' => 'Sudah Dibayarkan / Ditransfer',
                                    'Rejected' => 'Ditolak',
                                ])
                                ->default('Pending')
                                ->required(),

                            DatePicker::make('payment_date')
                                ->label('Tanggal Pembayaran / Transfer')
                                ->nullable(),
                        ]),

                        Textarea::make('description')
                            ->label('Keterangan / Rincian Pengeluaran')
                            ->placeholder('Jelaskan tujuan pengeluaran, nomor faktur, atau rute perjalanan')
                            ->rows(3),

                        FileUpload::make('receipt_photo')
                            ->label('Foto Bukti / Struk / Kwitansi Pembayaran')
                            ->disk('public')
                            ->directory('reimbursements/receipts')
                            ->image()
                            ->imageEditor()
                            ->maxSize(5120),
                    ]),
            ]);
    }
}
