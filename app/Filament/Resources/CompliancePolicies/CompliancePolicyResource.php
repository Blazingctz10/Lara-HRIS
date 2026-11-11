<?php

namespace App\Filament\Resources\CompliancePolicies;

use App\Filament\Resources\CompliancePolicies\Pages\CreateCompliancePolicy;
use App\Filament\Resources\CompliancePolicies\Pages\EditCompliancePolicy;
use App\Filament\Resources\CompliancePolicies\Pages\ListCompliancePolicies;
use App\Filament\Resources\CompliancePolicies\Schemas\CompliancePolicyForm;
use App\Filament\Resources\CompliancePolicies\Tables\CompliancePoliciesTable;
use App\Models\CompliancePolicy;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class CompliancePolicyResource extends Resource
{
    protected static ?string $model = CompliancePolicy::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|UnitEnum|null $navigationGroup = 'Kepatuhan & Tata Kelola';

    protected static ?string $navigationLabel = 'Kebijakan Kepatuhan';

    protected static ?string $modelLabel = 'Kebijakan Kepatuhan & SOP';

    protected static ?string $pluralModelLabel = 'Kebijakan Kepatuhan & Regulasi Bank';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->accessibleBy();
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'hr_admin', 'direksi', 'branch_manager']);
    }

    public static function canCreate(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'hr_admin']);
    }

    public static function canEdit(Model $record): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'hr_admin']);
    }

    public static function canDelete(Model $record): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'hr_admin']);
    }

    public static function canDeleteAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'hr_admin']);
    }

    public static function form(Schema $schema): Schema
    {
        return CompliancePolicyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompliancePoliciesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompliancePolicies::route('/'),
            'create' => CreateCompliancePolicy::route('/create'),
            'edit' => EditCompliancePolicy::route('/{record}/edit'),
        ];
    }
}

