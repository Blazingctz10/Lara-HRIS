<?php

namespace App\Filament\Resources\WarningLetters\Tables;

use App\Models\WarningLetter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WarningLettersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['employee.branch']))
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('employee.branch.name')
                    ->label('Kantor Cabang')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('level')
                    ->label('Tingkat Sanksi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Surat Peringatan 3 (SP 3)' => 'danger',
                        'Surat Peringatan 2 (SP 2)' => 'warning',
                        'Surat Peringatan 1 (SP 1)' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('letter_number')
                    ->label('Nomor Surat')
                    ->fontFamily('mono')
                    ->searchable(),

                TextColumn::make('reason')
                    ->label('Alasan Pelanggaran')
                    ->limit(35)
                    ->tooltip(fn (WarningLetter $record) => $record->reason),

                TextColumn::make('issued_date')
                    ->label('Tgl Terbit')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('expiry_date')
                    ->label('Tgl Berakhir')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Aktif' => 'danger',
                        'Dicabut / Pembinaan Berhasil' => 'success',
                        'Kedaluwarsa' => 'gray',
                        default => 'gray',
                    }),
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
