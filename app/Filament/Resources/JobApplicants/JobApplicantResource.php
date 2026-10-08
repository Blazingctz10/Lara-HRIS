<?php

namespace App\Filament\Resources\JobApplicants;

use App\Filament\Resources\JobApplicants\Pages\CreateJobApplicant;
use App\Filament\Resources\JobApplicants\Pages\EditJobApplicant;
use App\Filament\Resources\JobApplicants\Pages\ListJobApplicants;
use App\Filament\Resources\JobApplicants\Schemas\JobApplicantForm;
use App\Filament\Resources\JobApplicants\Tables\JobApplicantsTable;
use App\Models\JobApplicant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class JobApplicantResource extends Resource
{
    protected static ?string $model = JobApplicant::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Karyawan';

    protected static ?string $navigationLabel = 'Rekrutmen & Pelamar';

    protected static ?string $modelLabel = 'Pelamar Kerja';

    protected static ?string $pluralModelLabel = 'Pelamar Kerja (cPanel IMAP)';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'hr_admin', 'direksi']);
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

    public static function shouldRegisterNavigation(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'hr_admin', 'direksi']);
    }

    public static function getNavigationBadge(): ?string
    {
        $newCount = JobApplicant::where('status', 'Baru')->count();
        return $newCount > 0 ? (string) $newCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return JobApplicantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JobApplicantsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJobApplicants::route('/'),
            'create' => CreateJobApplicant::route('/create'),
            'edit' => EditJobApplicant::route('/{record}/edit'),
        ];
    }
}
