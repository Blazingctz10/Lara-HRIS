<?php

namespace App\Filament\Resources\Branches\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kantor')
                    ->description('Identitas unit kerja & klasifikasi kantor perbankan (Pusat, Cabang, Kas)')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('code')
                                ->label('Kode Kantor')
                                ->placeholder('Contoh: PUSAT-01 / CBG-01 / KAS-01')
                                ->required()
                                ->unique(ignoreRecord: true),

                            TextInput::make('name')
                                ->label('Nama Kantor')
                                ->placeholder('Contoh: Kantor Pusat BPR Banda Raya / Kantor Cabang Botania')
                                ->required(),

                            Select::make('type')
                                ->label('Tipe Kantor')
                                ->options([
                                    'Pusat' => 'Pusat (Kantor Pusat)',
                                    'Cabang' => 'Cabang (Kantor Cabang)',
                                    'Kas' => 'Kas (Kantor Kas)',
                                ])
                                ->default('Cabang')
                                ->required(),

                            TextInput::make('city')
                                ->label('Kota / Wilayah')
                                ->placeholder('Contoh: Jakarta Pusat')
                                ->required(),

                            TextInput::make('phone')
                                ->label('Nomor Telepon')
                                ->tel()
                                ->placeholder('Contoh: 021-3140002'),

                            Toggle::make('is_active')
                                ->label('Status Beroperasi')
                                ->default(true)
                                ->inline(false),
                        ]),

                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->rows(3)
                            ->required(),
                    ]),

                Section::make('Titik Lokasi Fisik Kantor')
                    ->description('Koordinat lokasi kantor untuk pemetaan aset fisik dan unit kerja BPR Banda Raya')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('latitude')
                                ->label('Latitude')
                                ->numeric()
                                ->placeholder('-6.19128000')
                                ->helperText('Format derajat desimal (contoh: -6.19128000)'),

                            TextInput::make('longitude')
                                ->label('Longitude')
                                ->numeric()
                                ->placeholder('106.82392000')
                                ->helperText('Format derajat desimal (contoh: 106.82392000)'),
                        ]),
                    ]),
            ]);
    }
}
