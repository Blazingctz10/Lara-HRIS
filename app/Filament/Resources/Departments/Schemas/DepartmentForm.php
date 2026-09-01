<?php

namespace App\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Divisi / Departemen')
                    ->description('Struktur departemen internal operasional & bisnis bank')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('code')
                                ->label('Kode Divisi')
                                ->placeholder('Contoh: RISK / LEND / OPS')
                                ->required()
                                ->unique(ignoreRecord: true),

                            TextInput::make('name')
                                ->label('Nama Divisi')
                                ->placeholder('Contoh: Kepatuhan & Manajemen Risiko')
                                ->required(),
                        ]),

                        Textarea::make('description')
                            ->label('Deskripsi Tugas & Fungsi')
                            ->rows(3),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                    ]),
            ]);
    }
}
