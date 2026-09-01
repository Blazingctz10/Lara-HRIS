<?php

namespace App\Filament\Resources\FingerprintDevices\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FingerprintDeviceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konfigurasi Terminal Mesin Fingerprint Solution X105')
                    ->description('Pengaturan IP Address LAN, Port ZK, dan Cabang Penempatan')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama Perangkat / Lokasi')
                                ->placeholder('Contoh: Solution X105 - KC Botania')
                                ->required(),

                            TextInput::make('model')
                                ->label('Model Mesin')
                                ->default('Solution X105')
                                ->required(),

                            Select::make('branch_id')
                                ->relationship('branch', 'name')
                                ->label('Kantor Cabang Penempatan')
                                ->required()
                                ->searchable()
                                ->preload(),

                            TextInput::make('serial_number')
                                ->label('Nomor Seri (Serial Number)')
                                ->placeholder('Contoh: SOL-X105-BOT01'),

                            TextInput::make('ip_address')
                                ->label('IP Address Mesin (LAN/VPN)')
                                ->placeholder('192.168.1.201')
                                ->required(),

                            TextInput::make('port')
                                ->label('Port ZK Protocol')
                                ->numeric()
                                ->default(4370)
                                ->required(),

                            TextInput::make('comm_key')
                                ->label('Comm Key / Password Mesin')
                                ->default('0')
                                ->required(),

                            Select::make('status')
                                ->label('Status Koneksi')
                                ->options([
                                    'Online' => '🟢 Online (Terhubung)',
                                    'Standby' => '🟡 Standby',
                                    'Offline' => '🔴 Offline (Terputus)',
                                ])
                                ->default('Online')
                                ->required(),

                            Toggle::make('is_active')
                                ->label('Aktifkan Sinkronisasi Otomatis')
                                ->default(true)
                                ->inline(false),
                        ]),
                    ]),
            ]);
    }
}
