<?php

namespace App\Filament\Portal\Resources\Overtimes;

use App\Filament\Portal\Resources\Overtimes\Pages\CreateOvertime;
use App\Filament\Portal\Resources\Overtimes\Pages\ListOvertimes;
use App\Filament\Portal\Resources\Overtimes\Schemas\OvertimeForm;
use App\Filament\Portal\Resources\Overtimes\Tables\OvertimesTable;
use App\Models\Overtime;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OvertimeResource extends Resource
{
    protected static ?string $model = Overtime::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-fire';

    protected static ?string $navigationLabel = 'Pengajuan Lembur';

    protected static ?string $modelLabel = 'Pengajuan Lembur';

    protected static ?string $pluralModelLabel = 'Riwayat Pengajuan Lembur';

    protected static ?int $navigationSort = 3;

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
        return OvertimeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OvertimesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOvertimes::route('/'),
            'create' => CreateOvertime::route('/create'),
        ];
    }
}
