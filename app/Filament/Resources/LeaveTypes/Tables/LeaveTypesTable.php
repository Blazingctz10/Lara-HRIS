<?php

namespace App\Filament\Resources\LeaveTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeaveTypesTable
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
                    ->label('Jenis Cuti')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('default_days')
                    ->label('Kuota Standar')
                    ->suffix(' Hari')
                    ->alignCenter(),

                IconColumn::make('is_block_leave')
                    ->label('Block Leave OJK')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('min_consecutive_days')
                    ->label('Min. Hari Beruntun')
                    ->suffix(' Hari')
                    ->placeholder('-')
                    ->alignCenter(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                //
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
