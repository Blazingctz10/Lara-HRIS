<?php

namespace App\Filament\Resources\Announcements;

use App\Filament\Resources\Announcements\Pages\CreateAnnouncement;
use App\Filament\Resources\Announcements\Pages\EditAnnouncement;
use App\Filament\Resources\Announcements\Pages\ListAnnouncements;
use App\Filament\Resources\Announcements\Schemas\AnnouncementForm;
use App\Filament\Resources\Announcements\Tables\AnnouncementsTable;
use App\Models\Announcement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static string|UnitEnum|null $navigationGroup = 'Komunikasi Internal';

    protected static ?string $navigationLabel = 'Memo & Pengumuman';

    protected static ?string $modelLabel = 'Pengumuman / Memo';

    protected static ?string $pluralModelLabel = 'Memo & Pengumuman Bank';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->accessibleBy();
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'hr_admin', 'direksi', 'branch_manager', 'compliance']);
    }

    public static function canCreate(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'hr_admin', 'direksi', 'compliance']);
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if (in_array($user->role, ['super_admin', 'hr_admin', 'compliance'], true)) {
            return true;
        }

        return $user->role === 'direksi' && (int) $record->author_id === (int) $user->id;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if (in_array($user->role, ['super_admin', 'hr_admin', 'compliance'], true)) {
            return true;
        }

        return $user->role === 'direksi' && (int) $record->author_id === (int) $user->id;
    }

    public static function canDeleteAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'hr_admin', 'compliance']);
    }

    public static function form(Schema $schema): Schema
    {
        return AnnouncementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnnouncementsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnnouncements::route('/'),
            'create' => CreateAnnouncement::route('/create'),
            'edit' => EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
