<?php

namespace App\Filament\Resources\LeaveTypes\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeaveTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konfigurasi Jenis Cuti & Regulasi Perbankan')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('code')
                                ->label('Kode Cuti')
                                ->placeholder('Contoh: ANNUAL / BLOCK_LEAVE')
                                ->required()
                                ->unique(ignoreRecord: true),

                            TextInput::make('name')
                                ->label('Nama Cuti')
                                ->placeholder('Contoh: Block Leave (Wajib OJK)')
                                ->required(),

                            TextInput::make('default_days')
                                ->label('Kuota Standar (Hari / Tahun)')
                                ->numeric()
                                ->default(12)
                                ->required(),

                            TextInput::make('min_consecutive_days')
                                ->label('Minimal Hari Berturut-turut')
                                ->numeric()
                                ->helperText('Isi 5 untuk Block Leave sesuai aturan OJK, kosongkan jika bebas'),

                            Toggle::make('is_block_leave')
                                ->label('Kategori Block Leave OJK (Mandatori)')
                                ->helperText('Aktifkan jika jenis cuti ini adalah program cuti wajib perbankan')
                                ->inline(false),

                            Toggle::make('is_active')
                                ->label('Status Aktif')
                                ->default(true)
                                ->inline(false),
                        ]),

                        Textarea::make('description')
                            ->label('Keterangan & Dasar Regulasi')
                            ->rows(3),
                    ]),
            ]);
    }
}
