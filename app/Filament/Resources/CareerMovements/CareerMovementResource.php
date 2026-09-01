<?php

namespace App\Filament\Resources\CareerMovements;

use App\Filament\Resources\CareerMovements\Pages\CreateCareerMovement;
use App\Filament\Resources\CareerMovements\Pages\EditCareerMovement;
use App\Filament\Resources\CareerMovements\Pages\ListCareerMovements;
use App\Filament\Resources\CareerMovements\Schemas\CareerMovementForm;
use App\Filament\Resources\CareerMovements\Tables\CareerMovementsTable;
use App\Models\CareerMovement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CareerMovementResource extends Resource
{
    protected static ?string $model = CareerMovement::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Karyawan';

    protected static ?string $navigationLabel = 'Mutasi & Promosi Karir';

    protected static ?string $modelLabel = 'Riwayat Mutasi / Promosi';

    protected static ?string $pluralModelLabel = 'Daftar Mutasi & Karir Pegawai';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->accessibleBy();
    }

    public static function form(Schema $schema): Schema
    {
        return CareerMovementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CareerMovementsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCareerMovements::route('/'),
            'create' => CreateCareerMovement::route('/create'),
            'edit' => EditCareerMovement::route('/{record}/edit'),
        ];
    }
}
