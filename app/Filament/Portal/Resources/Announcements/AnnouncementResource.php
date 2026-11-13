<?php

namespace App\Filament\Portal\Resources\Announcements;

use App\Filament\Portal\Resources\Announcements\Pages\ListAnnouncements;
use App\Filament\Portal\Resources\Announcements\Tables\AnnouncementsTable;
use App\Models\Announcement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static string|UnitEnum|null $navigationGroup = 'Komunikasi Internal';

    protected static ?string $navigationLabel = 'Memo & Pengumuman';

    protected static ?string $modelLabel = 'Memo & Pengumuman';

    protected static ?string $pluralModelLabel = 'Arsip Memo & Pengumuman Bank';

    protected static ?int $navigationSort = 11;

    public static function canViewAny(): bool
    {
        return auth()->check();
    }

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
        return parent::getEloquentQuery()->publishedForUser();
    }

    public static function table(Table $table): Table
    {
        return AnnouncementsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnnouncements::route('/'),
        ];
    }
}
