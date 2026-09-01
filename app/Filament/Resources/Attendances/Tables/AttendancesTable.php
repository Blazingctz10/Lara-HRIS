<?php

namespace App\Filament\Resources\Attendances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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

                TextColumn::make('employee.nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('employee.full_name')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('employee.branch.name')
                    ->label('Cabang')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('clock_in')
                    ->label('Masuk')
                    ->time('H:i')
                    ->color('success')
                    ->weight('bold'),

                TextColumn::make('clock_out')
                    ->label('Pulang')
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
                    ->label('Jarak GPS')
                    ->suffix(' m')
                    ->badge()
                    ->color(fn ($state) => $state <= 50 ? 'success' : 'danger')
                    ->placeholder('N/A'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Kehadiran')
                    ->options([
                        'Present' => 'Present',
                        'Late' => 'Late',
                        'Excused' => 'Excused',
                        'Absent' => 'Absent',
                    ]),
                SelectFilter::make('branch')
                    ->relationship('employee.branch', 'name')
                    ->label('Kantor Cabang'),
                Filter::make('today')
                    ->label('Hari Ini')
                    ->query(fn (Builder $query): Builder => $query->whereDate('date', now())),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
