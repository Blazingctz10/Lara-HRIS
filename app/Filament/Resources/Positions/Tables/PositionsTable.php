<?php

namespace App\Filament\Resources\Positions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PositionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Jabatan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('department.name')
                    ->label('Divisi')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('grade_level')
                    ->label('Grade / Level')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Division Head', 'Vice President' => 'danger',
                        'Manager', 'Assistant Manager' => 'warning',
                        'Supervisor', 'Officer' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('min_salary')
                    ->label('Standar Gaji Pokok')
                    ->formatStateUsing(fn ($state) => $state ? 'Rp ' . number_format((float) $state, 0, ',', '.') : 'Sesuai UMK')
                    ->color('success')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('department_id')
                    ->relationship('department', 'name')
                    ->label('Divisi'),
                SelectFilter::make('grade_level')
                    ->label('Grade Level')
                    ->options([
                        'Staff' => 'Staff',
                        'Officer' => 'Officer',
                        'Supervisor' => 'Supervisor',
                        'Assistant Manager' => 'Assistant Manager',
                        'Manager' => 'Manager',
                        'Vice President' => 'Vice President',
                        'Division Head' => 'Division Head',
                    ]),
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
