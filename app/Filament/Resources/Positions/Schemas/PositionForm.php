<?php

namespace App\Filament\Resources\Positions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PositionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Jabatan & Level Perbankan')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('department_id')
                                ->relationship('department', 'name')
                                ->label('Divisi / Departemen')
                                ->searchable()
                                ->preload()
                                ->required(),

                            TextInput::make('name')
                                ->label('Nama Jabatan')
                                ->placeholder('Contoh: Account Officer / Teller Frontliner')
                                ->required(),

                            Select::make('grade_level')
                                ->label('Level / Grade Jabatan')
                                ->options([
                                    'Staff' => 'Staff',
                                    'Officer' => 'Officer',
                                    'Supervisor' => 'Supervisor',
                                    'Assistant Manager' => 'Assistant Manager',
                                    'Manager' => 'Manager',
                                    'Vice President' => 'Vice President',
                                    'Division Head' => 'Division Head',
                                ])
                                ->default('Staff')
                                ->required(),

                            Toggle::make('is_active')
                                ->label('Status Aktif')
                                ->default(true),

                            TextInput::make('min_salary')
                                ->label('Standar Gaji Minimum (IDR)')
                                ->numeric()
                                ->prefix('Rp')
                                ->placeholder('5000000'),

                            TextInput::make('max_salary')
                                ->label('Standar Gaji Maksimum (IDR)')
                                ->numeric()
                                ->prefix('Rp')
                                ->placeholder('15000000'),
                        ]),
                    ]),
            ]);
    }
}
