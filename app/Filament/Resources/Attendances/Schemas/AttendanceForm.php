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
                Section::make('Data Presensi Biometrik (Mesin Sidik Jari)')
                    ->description('Pencatatan kehadiran staf bersumber dari mesin sidik jari biometrik (fingerprint) kantor cabang. Form ini dipergunakan untuk penyesuaian/koreksi manual jika terjadi kendala teknis.')
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
                        ]),

                        Textarea::make('notes')
                            ->label('Catatan Kehadiran / Keterangan Mesin')
                            ->placeholder('Contoh: Terdata otomatis dari Mesin Sidik Jari Solution X105 atau Koreksi HR manual')
                            ->rows(3),
                    ]),
            ]);
    }
}
