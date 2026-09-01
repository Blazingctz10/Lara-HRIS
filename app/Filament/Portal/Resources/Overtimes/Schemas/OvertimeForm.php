<?php

namespace App\Filament\Portal\Resources\Overtimes\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
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
        $employee = auth()->user()?->employee;

        return $schema
            ->components([
                Section::make('Permohonan Lembur Mandiri (SPK Lembur)')
                    ->description('Pengajuan lembur tugas akhir bulan / operasional kas / audit')
                    ->schema([
                        Hidden::make('employee_id')
                            ->default($employee?->id),

                        Hidden::make('status')
                            ->default('Pending'),

                        Grid::make(2)->schema([
                            DatePicker::make('date')
                                ->label('Tanggal Lembur')
                                ->default(now())
                                ->required(),

                            TextInput::make('duration_hours')
                                ->label('Durasi Lembur (Jam)')
                                ->numeric()
                                ->readOnly()
                                ->default(3.00)
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
                        ]),

                        Textarea::make('reason')
                            ->label('Uraian Tugas / Alasan Lembur')
                            ->placeholder('Jelaskan rincian pekerjaan lembur yang dilakukan')
                            ->required()
                            ->rows(3),
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
