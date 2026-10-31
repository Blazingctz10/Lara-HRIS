<?php

namespace App\Filament\Resources\Reimbursements;

use App\Filament\Resources\Reimbursements\Pages\CreateReimbursement;
use App\Filament\Resources\Reimbursements\Pages\EditReimbursement;
use App\Filament\Resources\Reimbursements\Pages\ListReimbursements;
use App\Filament\Resources\Reimbursements\Schemas\ReimbursementForm;
use App\Filament\Resources\Reimbursements\Tables\ReimbursementsTable;
use App\Models\Reimbursement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ReimbursementResource extends Resource
{
    protected static ?string $model = Reimbursement::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|UnitEnum|null $navigationGroup = 'Klaim & Reimbursement';

    protected static ?string $navigationLabel = 'Klaim Reimbursement & AO';

    protected static ?string $modelLabel = 'Klaim Reimbursement';

    protected static ?string $pluralModelLabel = 'Daftar Klaim Reimbursement Pegawai';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $unpaidCount = static::getEloquentQuery()->whereIn('status', ['Pending', 'Approved_Pincab'])->count();
        return $unpaidCount > 0 ? (string) $unpaidCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

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

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'hr_admin']);
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'hr_admin']);
    }

    public static function canDeleteAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'hr_admin']);
    }

    public static function form(Schema $schema): Schema
    {
        return ReimbursementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReimbursementsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReimbursements::route('/'),
            'create' => CreateReimbursement::route('/create'),
            'edit' => EditReimbursement::route('/{record}/edit'),
        ];
    }
}
