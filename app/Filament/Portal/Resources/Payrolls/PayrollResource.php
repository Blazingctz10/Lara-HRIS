<?php

namespace App\Filament\Portal\Resources\Payrolls;

use App\Filament\Portal\Resources\Payrolls\Pages\ListPayrolls;
use App\Filament\Portal\Resources\Payrolls\Schemas\PayrollForm;
use App\Filament\Portal\Resources\Payrolls\Tables\PayrollsTable;
use App\Models\Payroll;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Slip Gaji Saya';

    protected static ?string $modelLabel = 'Slip Gaji';

    protected static ?string $pluralModelLabel = 'Riwayat Slip Gaji';

    protected static ?int $navigationSort = 3;

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
        $employee = auth()->user()?->employee;

        if (! $employee) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->where('employee_id', $employee->id)
            ->whereIn('status', ['Approved', 'Paid']);
    }

    public static function form(Schema $schema): Schema
    {
        return PayrollForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PayrollsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrolls::route('/'),
        ];
    }
}
