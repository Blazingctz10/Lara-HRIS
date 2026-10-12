<?php

namespace App\Filament\Resources\AttendanceDispensations\Tables;

use App\Models\AttendanceDispensation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AttendanceDispensationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['employee.branch', 'recorder']))
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
                    ->sortable(),

                TextColumn::make('dispensation_type')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Izin via Chat/WhatsApp' => 'warning',
                        'Tugas Lapangan GA' => 'primary',
                        'Tugas Dinas Luar' => 'success',
                        'Sakit Mendadak' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('custom_reason')
                    ->label('Alasan / Isi Sendiri')
                    ->placeholder('-')
                    ->searchable()
                    ->limit(25),

                TextColumn::make('reference_contact')
                    ->label('Referensi Masuk')
                    ->placeholder('-')
                    ->limit(20),

                TextColumn::make('description')
                    ->label('Catatan HR/GA')
                    ->limit(35)
                    ->tooltip(fn ($record) => $record->description)
                    ->placeholder('-'),

                TextColumn::make('recorder.name')
                    ->label('Dicatat Oleh')
                    ->placeholder('-')
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('dispensation_type')
                    ->label('Kategori')
                    ->options([
                        'Izin via Chat/WhatsApp' => 'Izin via Chat/WhatsApp',
                        'Tugas Lapangan GA' => 'Tugas Lapangan GA',
                        'Tugas Dinas Luar' => 'Tugas Dinas Luar',
                        'Sakit Mendadak' => 'Sakit Mendadak',
                        'Lainnya / Isi Sendiri' => 'Lainnya / Isi Sendiri',
                    ]),
                SelectFilter::make('branch')
                    ->relationship('employee.branch', 'name')
                    ->label('Cabang'),
            ])
            ->recordActions([
                EditAction::make()
                    ->button()
                    ->outlined()
                    ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin', 'branch_manager'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
            ]);
    }
}
