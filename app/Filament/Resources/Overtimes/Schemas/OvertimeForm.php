<?php

namespace App\Filament\Resources\Overtimes\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class OvertimeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Surat Perintah Kerja Lembur (SPK Lembur)')
                    ->description('Pencatatan jam kerja lembur pegawai dan alasan operasional perbankan')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('employee_id')
                                ->relationship('employee', 'full_name')
                                ->label('Nama Pegawai')
                                ->searchable()
                                ->preload()
                                ->required(),

                            DatePicker::make('date')
                                ->label('Tanggal Lembur')
                                ->default(now())
                                ->required(),

                            TimePicker::make('start_time')
                                ->label('Jam Mulai Lembur')
                                ->default('17:30:00')
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateDuration($get, $set)),

                            TimePicker::make('end_time')
                                ->label('Jam Selesai Lembur')
                                ->default('20:30:00')
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateDuration($get, $set)),

                            TextInput::make('duration_hours')
                                ->label('Durasi (Jam)')
                                ->numeric()
                                ->readOnly()
                                ->default(3.00)
                                ->required(),

                            Select::make('status')
                                ->label('Status Approval')
                                ->options([
                                    'Pending' => 'Menunggu Approval Pincab',
                                    'Approved_Pincab' => 'Disetujui Pincab',
                                    'Approved_HR' => 'Disetujui HR HO (Siap Cair)',
                                    'Rejected' => 'Ditolak',
                                ])
                                ->default('Pending')
                                ->required(),
                        ]),

                        Textarea::make('reason')
                            ->label('Uraian Tugas / Alasan Lembur')
                            ->placeholder('Contoh: Tutup Buku Akhir Bulan (EOM Closing), Rekonsiliasi Kas Kliring, atau Audit OJK')
                            ->required()
                            ->rows(3),

                        Textarea::make('rejection_note')
                            ->label('Catatan Penolakan (Jika Ada)')
                            ->rows(2)
                            ->visible(fn (Get $get) => $get('status') === 'Rejected'),
                    ]),
            ]);
    }

    private static function calculateDuration(Get $get, Set $set): void
    {
        $start = $get('start_time');
        $end = $get('end_time');

        if ($start && $end) {
            $startTime = Carbon::parse($start);
            $endTime = Carbon::parse($end);

            if ($endTime->gt($startTime)) {
                $hours = round($endTime->diffInMinutes($startTime) / 60, 2);
                $set('duration_hours', $hours);
            }
        }
    }
}
