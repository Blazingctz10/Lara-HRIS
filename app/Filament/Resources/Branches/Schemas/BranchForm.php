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
                Section::make('Informasi Kantor Cabang')
                    ->description('Identitas unit kerja & klasifikasi kantor perbankan')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('code')
                                ->label('Kode Cabang')
                                ->placeholder('Contoh: KC-001 / HO-001')
                                ->required()
                                ->unique(ignoreRecord: true),

                            TextInput::make('name')
                                ->label('Nama Kantor / Cabang')
                                ->placeholder('Contoh: KC Jakarta Thamrin')
                                ->required(),

                            Select::make('type')
                                ->label('Tipe Kantor')
                                ->options([
                                    'Head Office' => 'Head Office (Kantor Pusat)',
                                    'Main Branch' => 'Main Branch (KC)',
                                    'Sub Branch' => 'Sub Branch (KCP)',
                                    'Cash Office' => 'Cash Office (KK)',
                                ])
                                ->default('Main Branch')
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

                Section::make('Geofencing & Presensi GPS')
                    ->description('Koordinat fisik kantor untuk validasi presensi mobile/portal staf')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('latitude')
                                ->label('Latitude GPS')
                                ->numeric()
                                ->placeholder('-6.19128000')
                                ->helperText('Format derajat desimal (contoh: -6.19128000)'),

                            TextInput::make('longitude')
                                ->label('Longitude GPS')
                                ->numeric()
                                ->placeholder('106.82392000')
                                ->helperText('Format derajat desimal (contoh: 106.82392000)'),

                            TextInput::make('radius_meters')
                                ->label('Radius Toleransi')
                                ->numeric()
                                ->default(50)
                                ->suffix('meter')
                                ->helperText('Jarak maksimal staf dari titik kantor saat absen'),
                        ]),
                    ]),
            ]);
    }
}
