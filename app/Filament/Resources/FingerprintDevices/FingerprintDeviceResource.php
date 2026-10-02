<?php

namespace App\Filament\Resources\FingerprintDevices;

use App\Filament\Resources\FingerprintDevices\Pages\CreateFingerprintDevice;
use App\Filament\Resources\FingerprintDevices\Pages\EditFingerprintDevice;
use App\Filament\Resources\FingerprintDevices\Pages\ListFingerprintDevices;
use App\Filament\Resources\FingerprintDevices\Schemas\FingerprintDeviceForm;
use App\Filament\Resources\FingerprintDevices\Tables\FingerprintDevicesTable;
use App\Models\FingerprintDevice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class FingerprintDeviceResource extends Resource
{
    protected static ?string $model = FingerprintDevice::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-finger-print';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan & Sistem';

    protected static ?string $navigationLabel = 'Mesin Presensi (Solution X105)';

    protected static ?string $modelLabel = 'Mesin Fingerprint Solution X105';

    protected static ?string $pluralModelLabel = 'Integrasi Mesin Fingerprint Solution X105';

    protected static ?int $navigationSort = 96;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->accessibleBy();
    }

    public static function form(Schema $schema): Schema
    {
        return FingerprintDeviceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FingerprintDevicesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFingerprintDevices::route('/'),
            'create' => CreateFingerprintDevice::route('/create'),
            'edit' => EditFingerprintDevice::route('/{record}/edit'),
        ];
    }
}
