<?php

namespace App\Filament\Resources\Attendances\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Presensi Harian')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('employee_id')
                                ->relationship('employee', 'full_name')
                                ->label('Pegawai')
                                ->searchable()
                                ->preload()
                                ->required(),

                            DatePicker::make('date')
                                ->label('Tanggal')
                                ->default(now())
                                ->required()
                                ->native(false),

                            Select::make('status')
                                ->label('Status Kehadiran')
                                ->options([
                                    'Present' => 'Present (Hadir Tepat Waktu)',
                                    'Late' => 'Late (Terlambat)',
                                    'Excused' => 'Excused (Izin / Sakit)',
                                    'Absent' => 'Absent (Mangkir)',
                                ])
                                ->default('Present')
                                ->required(),

                            TimePicker::make('clock_in')
                                ->label('Jam Masuk')
                                ->seconds(false),

                            TimePicker::make('clock_out')
                                ->label('Jam Pulang')
                                ->seconds(false),

                            TextInput::make('distance_in_meters')
                                ->label('Deviasi Jarak Geofence')
                                ->numeric()
                                ->suffix('meter')
                                ->helperText('Jarak aktual staf dari kantor saat absensi'),
                        ]),

                        Grid::make(2)->schema([
                            FileUpload::make('proof_photo')
                                ->label('Foto Selfie / Bukti Presensi')
                                ->image()
                                ->directory('attendances/proofs')
                                ->maxSize(5120),

                            Textarea::make('notes')
                                ->label('Catatan Kehadiran')
                                ->rows(3),
                        ]),
                    ]),
            ]);
    }
}
