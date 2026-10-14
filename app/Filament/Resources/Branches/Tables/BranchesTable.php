<?php

namespace App\Filament\Resources\Branches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nama Kantor')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Pusat', 'Head Office' => 'Pusat',
                        'Cabang', 'Main Branch', 'Sub Branch' => 'Cabang',
                        'Kas', 'Cash Office' => 'Kas',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Pusat', 'Head Office' => 'danger',
                        'Cabang', 'Main Branch', 'Sub Branch' => 'success',
                        'Kas', 'Cash Office' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('city')
                    ->label('Kota')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('employees_count')
                    ->counts('employees')
                    ->label('Jumlah Staf')
                    ->alignCenter()
                    ->badge()
                    ->color('secondary'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe Kantor')
                    ->options([
                        'Pusat' => 'Pusat',
                        'Cabang' => 'Cabang',
                        'Kas' => 'Kas',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Status Operasional'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->button()
                    ->outlined(),
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
