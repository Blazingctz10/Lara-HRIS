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
                    ->color(fn (string $state): string => match ($state) {
                        'Head Office' => 'danger',
                        'Main Branch' => 'success',
                        'Sub Branch' => 'info',
                        'Cash Office' => 'warning',
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
                        'Head Office' => 'Head Office',
                        'Main Branch' => 'Main Branch',
                        'Sub Branch' => 'Sub Branch',
                        'Cash Office' => 'Cash Office',
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
                    ->outlined(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
