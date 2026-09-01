<?php

namespace App\Filament\Resources\CareerMovements\Tables;

use App\Models\CareerMovement;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CareerMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Mutasi Cabang' => 'primary',
                        'Promosi Jabatan' => 'success',
                        'Pengangkatan Tetap' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('sk_number')
                    ->label('Nomor SK')
                    ->fontFamily('mono')
                    ->searchable(),

                TextColumn::make('fromBranch.name')
                    ->label('Dari Cabang')
                    ->placeholder('-'),

                TextColumn::make('toBranch.name')
                    ->label('Ke Cabang')
                    ->badge()
                    ->color('primary')
                    ->placeholder('-'),

                TextColumn::make('toPosition.name')
                    ->label('Jabatan Baru')
                    ->placeholder('-'),

                TextColumn::make('effective_date')
                    ->label('Tgl Efektif')
                    ->date('d M Y')
                    ->sortable(),
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
