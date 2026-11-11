<?php

namespace App\Filament\Portal\Resources\CompliancePolicies;

use App\Filament\Portal\Resources\CompliancePolicies\Pages\ListCompliancePolicies;
use App\Filament\Portal\Resources\CompliancePolicies\Tables\CompliancePoliciesTable;
use App\Models\CompliancePolicy;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class CompliancePolicyResource extends Resource
{
    protected static ?string $model = CompliancePolicy::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|UnitEnum|null $navigationGroup = 'Pedoman & Regulasi';

    protected static ?string $navigationLabel = 'Kebijakan Kepatuhan';

    protected static ?string $modelLabel = 'Kebijakan Kepatuhan';

    protected static ?string $pluralModelLabel = 'Katalog Kebijakan Kepatuhan & SOP Bank';

    protected static ?int $navigationSort = 10;

    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->accessibleBy();
    }

    public static function table(Table $table): Table
    {
        return CompliancePoliciesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompliancePolicies::route('/'),
        ];
    }
}

