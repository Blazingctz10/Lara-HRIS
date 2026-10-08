<?php

namespace App\Filament\Resources\AttendanceDispensations;

use App\Filament\Resources\AttendanceDispensations\Pages\CreateAttendanceDispensation;
use App\Filament\Resources\AttendanceDispensations\Pages\EditAttendanceDispensation;
use App\Filament\Resources\AttendanceDispensations\Pages\ListAttendanceDispensations;
use App\Filament\Resources\AttendanceDispensations\Schemas\AttendanceDispensationForm;
use App\Filament\Resources\AttendanceDispensations\Tables\AttendanceDispensationsTable;
use App\Models\AttendanceDispensation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AttendanceDispensationResource extends Resource
{
    protected static ?string $model = AttendanceDispensation::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|UnitEnum|null $navigationGroup = 'Presensi & Kehadiran';

    protected static ?string $navigationLabel = 'Logbook Dispensasi HR & GA';

    protected static ?string $modelLabel = 'Logbook Dispensasi';

    protected static ?string $pluralModelLabel = 'Logbook Dispensasi Kehadiran';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isSuperAdmin() || auth()->user()?->isHrAdmin() || auth()->user()?->isBranchManager() || auth()->user()?->isDireksi();
    }

    public static function canCreate(): bool
    {
        // Direksi hanya read-only, create hanya untuk HR Admin, Super Admin, dan Pincab
        return auth()->user()?->isSuperAdmin() || auth()->user()?->isHrAdmin() || auth()->user()?->isBranchManager();
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->isSuperAdmin() || auth()->user()?->isHrAdmin() || auth()->user()?->isBranchManager();
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->isSuperAdmin() || auth()->user()?->isHrAdmin();
    }

    public static function getEloquentQuery(): Builder
    {
        return AttendanceDispensation::accessibleBy();
    }

    public static function form(Schema $schema): Schema
    {
        return AttendanceDispensationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendanceDispensationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendanceDispensations::route('/'),
            'create' => CreateAttendanceDispensation::route('/create'),
            'edit' => EditAttendanceDispensation::route('/{record}/edit'),
        ];
    }
}
