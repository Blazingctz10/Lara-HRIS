<?php

namespace App\Filament\Resources\CareerMovements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CareerMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Surat Keputusan Mutasi, Promosi, & Rotasi Jabatan')
                    ->description('Pencatatan riwayat perubahan cabang atau promosi jenjang karir pegawai')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('employee_id')
                                ->relationship('employee', 'full_name')
                                ->label('Nama Pegawai')
                                ->searchable()
                                ->preload()
                                ->required(),

                            Select::make('type')
                                ->label('Jenis Perubahan Karir')
                                ->options([
                                    'Mutasi Cabang' => 'Mutasi Antar Kantor Cabang',
                                    'Promosi Jabatan' => 'Promosi Kenaikan Jabatan',
                                    'Rotasi Divisi' => 'Rotasi Antar Divisi / Bagian',
                                    'Pengangkatan Tetap' => 'Pengangkatan Pegawai Tetap (PKWTT)',
                                    'Demosi' => 'Demosi / Penyesuaian',
                                ])
                                ->default('Mutasi Cabang')
                                ->required(),

                            DatePicker::make('effective_date')
                                ->label('Tanggal Efektif Berlaku')
                                ->default(now())
                                ->required(),

                            TextInput::make('sk_number')
                                ->label('Nomor SK Direksi')
                                ->placeholder('Contoh: SK.DIR/BPR-BR/2026/08/042')
                                ->required(),

                            Select::make('from_branch_id')
                                ->relationship('fromBranch', 'name')
                                ->label('Dari Cabang Asal')
                                ->searchable()
                                ->preload(),

                            Select::make('to_branch_id')
                                ->relationship('toBranch', 'name')
                                ->label('Ke Cabang Tujuan')
                                ->searchable()
                                ->preload(),

                            Select::make('from_position_id')
                                ->relationship('fromPosition', 'name')
                                ->label('Dari Jabatan Asal')
                                ->searchable()
                                ->preload(),

                            Select::make('to_position_id')
                                ->relationship('toPosition', 'name')
                                ->label('Ke Jabatan Baru')
                                ->searchable()
                                ->preload(),
                        ]),

                        Textarea::make('notes')
                            ->label('Catatan Pertimbangan / Rekomendasi')
                            ->rows(3),

                        FileUpload::make('sk_attachment')
                            ->label('Upload Salinan Dokumen SK (.PDF)')
                            ->directory('career_movements/sk_files')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->maxSize(5120),
                    ]),
            ]);
    }
}
