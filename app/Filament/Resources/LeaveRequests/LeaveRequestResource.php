<?php

namespace App\Filament\Resources\LeaveRequests;

use App\Filament\Resources\LeaveRequests\Pages\CreateLeaveRequest;
use App\Filament\Resources\LeaveRequests\Pages\EditLeaveRequest;
use App\Filament\Resources\LeaveRequests\Pages\ListLeaveRequests;
use App\Filament\Resources\LeaveRequests\Schemas\LeaveRequestForm;
use App\Filament\Resources\LeaveRequests\Tables\LeaveRequestsTable;
use App\Models\LeaveRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Cuti';

    protected static ?string $navigationLabel = 'Persetujuan Cuti';

    protected static ?string $modelLabel = 'Pengajuan Cuti';

    protected static ?string $pluralModelLabel = 'Daftar Pengajuan Cuti';

    protected static ?int $navigationSort = 2;

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
