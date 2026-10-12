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
            ->modifyQueryUsing(fn ($query) => $query->with(['employee.branch']))
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

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

                TextColumn::make('late_minutes')
                    ->label('Terlambat')
                    ->formatStateUsing(fn ($state) => $state > 0 ? "{$state} mnt" : '-')
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'gray')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('penalty_amount')
                    ->label('Denda (Rp)')
                    ->money('IDR', locale: 'id')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'gray')
                    ->sortable(),

                TextColumn::make('source_device')
                    ->label('Metode Presensi')
                    ->state(fn ($record) => $record->status === 'Excused' ? 'Logbook HR/GA' : 'Mesin Sidik Jari')
                    ->badge()
                    ->color(fn ($record) => $record->status === 'Excused' ? 'info' : 'success')
                    ->icon(fn ($record) => $record->status === 'Excused' ? 'heroicon-m-clipboard-document-list' : 'heroicon-m-finger-print'),

                TextColumn::make('notes')
                    ->label('Keterangan / Dispensasi')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->notes)
                    ->placeholder('-'),
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
                EditAction::make()
                    ->button()
                    ->outlined()
                    ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
            ]);
    }
}
