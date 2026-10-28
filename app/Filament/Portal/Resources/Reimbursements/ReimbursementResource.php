<?php

namespace App\Filament\Portal\Resources\Reimbursements;

use App\Filament\Portal\Resources\Reimbursements\Pages\CreateReimbursement;
use App\Filament\Portal\Resources\Reimbursements\Pages\ListReimbursements;
use App\Filament\Portal\Resources\Reimbursements\Schemas\ReimbursementForm;
use App\Filament\Portal\Resources\Reimbursements\Tables\ReimbursementsTable;
use App\Models\Reimbursement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ReimbursementResource extends Resource
{
    protected static ?string $model = Reimbursement::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $navigationLabel = 'Klaim Reimbursement';

    protected static ?string $modelLabel = 'Klaim Reimbursement';

    protected static ?string $pluralModelLabel = 'Riwayat Klaim Reimbursement Saya';

    protected static ?int $navigationSort = 5;

    public static function getNavigationBadge(): ?string
    {
        $employee = auth()->user()?->employee;
        if (! $employee) {
            return null;
        }

        $activeCount = Reimbursement::where('employee_id', $employee->id)
            ->whereIn('status', ['Pending', 'Approved_Pincab'])
            ->count();

        return $activeCount > 0 ? (string) $activeCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->employee !== null;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->employee !== null;
    }

    public static function canEdit(Model $record): bool
    {
        return $record->status === 'Pending' && $record->employee_id === auth()->user()?->employee?->id;
    }

    public static function canDelete(Model $record): bool
    {
        return $record->status === 'Pending' && $record->employee_id === auth()->user()?->employee?->id;
    }

    public static function getEloquentQuery(): Builder
    {
        $employee = auth()->user()?->employee;

        if (! $employee) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()->where('employee_id', $employee->id);
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
        ];
    }
}
