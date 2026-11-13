<?php

namespace App\Filament\Portal\Resources\Attendances\Tables;

use App\Models\Attendance;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->description(fn ($record) => $record->date ? \Carbon\Carbon::parse($record->date)->translatedFormat('l') : null)
                    ->sortable(),

                TextColumn::make('source_device')
                    ->label('Metode Log')
                    ->state(function ($record) {
                        if (str_contains($record->notes ?? '', 'Dispensasi Telat')) {
                            return 'Izin Telat WA';
                        }
                        if (str_contains($record->notes ?? '', 'Cuti Resmi') || str_contains($record->notes ?? '', 'Cuti')) {
                            return 'Cuti Resmi ESS';
                        }
                        return $record->status === 'Excused' ? 'Logbook HR/GA' : 'Mesin Fingerprint';
                    })
                    ->badge()
                    ->color(function ($record) {
                        if (str_contains($record->notes ?? '', 'Dispensasi Telat')) {
                            return 'warning';
                        }
                        return $record->status === 'Excused' ? 'info' : 'success';
                    })
                    ->icon(function ($record) {
                        if (str_contains($record->notes ?? '', 'Dispensasi Telat')) {
                            return 'heroicon-o-clock';
                        }
                        if (str_contains($record->notes ?? '', 'Cuti')) {
                            return 'heroicon-o-document-check';
                        }
                        return $record->status === 'Excused' ? 'heroicon-o-clipboard-document-list' : 'heroicon-o-finger-print';
                    }),

                TextColumn::make('clock_in')
                    ->label('Jam Masuk')
                    ->time('H:i')
                    ->color('success')
                    ->weight('bold')
                    ->alignCenter(),

                TextColumn::make('clock_out')
                    ->label('Jam Pulang')
                    ->time('H:i')
                    ->color('primary')
                    ->placeholder('--:--')
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(function ($state, $record) {
                        if ($state === 'Present' && str_contains($record->notes ?? '', 'Dispensasi Telat')) {
                            return 'Hadir (Dispensasi)';
                        }
                        return match ($state) {
                            'Present' => 'Hadir',
                            'Late' => 'Terlambat',
                            'Excused' => str_contains($record->notes ?? '', 'Cuti') ? 'Cuti Resmi' : 'Izin / Sakit',
                            'Absent' => 'Alpa',
                            default => $state,
                        };
                    })
                    ->color(fn (string $state, $record): string => match ($state) {
                        'Present' => str_contains($record->notes ?? '', 'Dispensasi Telat') ? 'info' : 'success',
                        'Late' => 'warning',
                        'Excused' => 'info',
                        'Absent' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('late_and_penalty')
                    ->label('Keterlambatan & Denda')
                    ->state(function ($record) {
                        if ($record->late_minutes > 0) {
                            $penalty = $record->penalty_amount > 0
                                ? 'Rp ' . number_format($record->penalty_amount, 0, ',', '.')
                                : 'Bebas Denda';
                            return "{$record->late_minutes} mnt • {$penalty}";
                        }
                        if ($record->status === 'Late') {
                            return 'Terlambat (Rp 0)';
                        }
                        return 'Tepat Waktu';
                    })
                    ->badge()
                    ->color(function ($record) {
                        if ($record->penalty_amount > 0) {
                            return 'danger';
                        }
                        if ($record->late_minutes > 0) {
                            return 'warning';
                        }
                        return 'gray';
                    })
                    ->alignCenter(),

                TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(40)
                    ->wrap()
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Kehadiran')
                    ->options([
                        'Present' => 'Hadir',
                        'Late' => 'Terlambat',
                        'Excused' => 'Izin / Cuti / Sakit',
                        'Absent' => 'Alpa',
                    ]),
            ])
            ->recordActions([
                Action::make('detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading('Rincian Presensi Saya')
                    ->modalDescription(fn (Attendance $record) => $record->date ? \Carbon\Carbon::parse($record->date)->translatedFormat('l, d F Y') : '-')
                    ->modalContent(function (Attendance $record) {
                        $inTime = $record->clock_in ? \Carbon\Carbon::parse($record->clock_in)->format('H:i') : '--:--';
                        $outTime = $record->clock_out ? \Carbon\Carbon::parse($record->clock_out)->format('H:i') : '--:--';
                        $penalty = $record->penalty_amount > 0
                            ? 'Rp ' . number_format((float) $record->penalty_amount, 0, ',', '.')
                            : 'Rp 0';

                        $html = '<div style="line-height: 1.65; font-size: 0.9rem;">';
                        $html .= '<p><strong>Jam Masuk / Pulang:</strong> ' . e($inTime) . ' / ' . e($outTime) . '</p>';
                        $html .= '<p><strong>Terlambat:</strong> ' . (int) $record->late_minutes . ' menit &bull; <strong>Denda:</strong> ' . e($penalty) . '</p>';
                        $html .= '<div style="margin-top: 0.85rem; padding: 0.85rem 1rem; border-radius: 14px; background: rgba(14, 104, 56, 0.06);">';
                        $html .= '<strong>Catatan:</strong><br>' . nl2br(e($record->notes ?: 'Tidak ada catatan tambahan.'));
                        $html .= '</div>';
                        $html .= '</div>';

                        return new HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
            ]);
    }
}
