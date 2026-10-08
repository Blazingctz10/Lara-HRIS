<?php

namespace App\Filament\Resources\WarningLetters;

use App\Filament\Resources\WarningLetters\Pages\CreateWarningLetter;
use App\Filament\Resources\WarningLetters\Pages\EditWarningLetter;
use App\Filament\Resources\WarningLetters\Pages\ListWarningLetters;
use App\Filament\Resources\WarningLetters\Schemas\WarningLetterForm;
use App\Filament\Resources\WarningLetters\Tables\WarningLettersTable;
use App\Models\WarningLetter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class WarningLetterResource extends Resource
{
    protected static ?string $model = WarningLetter::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Karyawan';

    protected static ?string $navigationLabel = 'Surat Peringatan (SP) & Sanksi';

    protected static ?string $modelLabel = 'Surat Peringatan (SP)';

    protected static ?string $pluralModelLabel = 'Daftar Surat Peringatan Pegawai';

    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->accessibleBy();
    }

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

    public static function form(Schema $schema): Schema
    {
        return WarningLetterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WarningLettersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWarningLetters::route('/'),
            'create' => CreateWarningLetter::route('/create'),
            'edit' => EditWarningLetter::route('/{record}/edit'),
        ];
    }
}
