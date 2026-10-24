<?php

namespace App\Filament\Portal\Resources\LeaveRequests;

use App\Filament\Portal\Resources\LeaveRequests\Pages\CreateLeaveRequest;
use App\Filament\Portal\Resources\LeaveRequests\Pages\EditLeaveRequest;
use App\Filament\Portal\Resources\LeaveRequests\Pages\ListLeaveRequests;
use App\Filament\Portal\Resources\LeaveRequests\Schemas\LeaveRequestForm;
use App\Filament\Portal\Resources\LeaveRequests\Tables\LeaveRequestsTable;
use App\Models\LeaveRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationLabel = 'Pengajuan Cuti Saya';

    protected static ?string $modelLabel = 'Pengajuan Cuti';

    protected static ?string $pluralModelLabel = 'Riwayat Cuti Saya';

    protected static ?int $navigationSort = 2;

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
        return LeaveRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeaveRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeaveRequests::route('/'),
            'create' => CreateLeaveRequest::route('/create'),
            'edit' => EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
