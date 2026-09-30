<?php

namespace App\Filament\Resources\WarningLetters\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class WarningLetterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pencatatan Surat Peringatan (SP) & Pembinaan SDM')
                    ->description('Dokumentasi sanksi disiplin dan kepatuhan perbankan')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('employee_id')
                                ->relationship('employee', 'full_name')
                                ->label('Nama Pegawai Terkait')
                                ->searchable()
                                ->preload()
                                ->required(),

                            Select::make('level')
                                ->label('Tingkat Sanksi / SP')
                                ->options([
                                    'Teguran Lisan' => 'Teguran Lisan / Konseling',
                                    'Surat Peringatan 1 (SP 1)' => 'Surat Peringatan 1 (SP 1)',
                                    'Surat Peringatan 2 (SP 2)' => 'Surat Peringatan 2 (SP 2)',
                                    'Surat Peringatan 3 (SP 3)' => 'Surat Peringatan 3 (SP 3)',
                                ])
                                ->default('Surat Peringatan 1 (SP 1)')
                                ->required(),

                            TextInput::make('letter_number')
                                ->label('Nomor Surat Peringatan')
                                ->placeholder('Contoh: SP.01/SDM/BPR-BR/2026/012')
                                ->required(),

                            DatePicker::make('issued_date')
                                ->label('Tanggal Diterbitkan')
                                ->default(now())
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    $issued = $get('issued_date');
                                    if ($issued) {
                                        // Standard banking SP validity is 6 months
                                        $set('expiry_date', Carbon::parse($issued)->addMonths(6)->toDateString());
                                    }
                                }),

                            DatePicker::make('expiry_date')
                                ->label('Tanggal Berakhir Masa Berlaku (Default: 6 Bulan)')
                                ->default(now()->addMonths(6))
                                ->required(),

                            Select::make('status')
                                ->label('Status SP')
                                ->options([
                                    'Aktif' => 'Aktif (Dalam Masa Berlaku)',
                                    'Kedaluwarsa' => 'Kedaluwarsa (Lewat 6 Bulan)',
                                    'Dicabut / Pembinaan Berhasil' => 'Dicabut / Pembinaan Berhasil',
                                ])
                                ->default('Aktif')
                                ->required(),
                        ]),

                        Textarea::make('reason')
                            ->label('Kronologi & Pelanggaran SOP / Kode Etik Bank')
                            ->placeholder('Jelaskan rincian temuan pelanggaran, selisih kas, atau ketidakdisiplinan kerja')
                            ->required()
                            ->rows(3),

                        Textarea::make('notes')
                            ->label('Rekomendasi / Rencana Pembinaan SDM')
                            ->rows(2),

                        FileUpload::make('attachment_url')
                            ->label('Upload Dokumen Surat SP / Berita Acara (.PDF / Foto)')
                            ->directory('warning_letters/docs')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->maxSize(5120),
                    ]),
            ]);
    }
}
