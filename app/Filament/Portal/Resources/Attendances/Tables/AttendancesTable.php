<?php

namespace App\Filament\Portal\Resources\Attendances\Tables;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
                    ->sortable(),

                ImageColumn::make('proof_photo')
                    ->label('Selfie')
                    ->circular()
                    ->defaultImageUrl('https://ui-avatars.com/api/?name=Presensi&background=f1f5f9&color=64748b'),

                TextColumn::make('clock_in')
                    ->label('Jam Masuk')
                    ->time('H:i')
                    ->color('success')
                    ->weight('bold'),

                TextColumn::make('clock_out')
                    ->label('Jam Pulang')
                    ->time('H:i')
                    ->color('primary')
                    ->placeholder('--:--'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Present' => 'success',
                        'Late' => 'warning',
                        'Excused' => 'info',
                        'Absent' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('distance_in_meters')
                    ->label('Deviasi Jarak')
                    ->suffix(' m')
                    ->badge()
                    ->color(fn ($state) => $state <= 50 ? 'success' : 'danger')
                    ->placeholder('N/A'),

                TextColumn::make('notes')
                    ->label('Catatan')
                    ->placeholder('-'),
            ])
            ->recordActions([
                //
            ]);
    }
}
