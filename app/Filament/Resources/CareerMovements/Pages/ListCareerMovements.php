<?php

namespace App\Filament\Resources\CareerMovements\Pages;

use App\Filament\Resources\CareerMovements\CareerMovementResource;
use App\Models\CareerMovement;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListCareerMovements extends ListRecords
{
    protected static string $resource = CareerMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Catat Mutasi / Promosi')
                ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
        ];
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make('Pegawai Aktif')
                ->icon('heroicon-o-check-circle')
                ->badge(CareerMovement::whereHas('employee', fn ($q) => $q->where('status', 'Active'))->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('employee', fn ($q) => $q->where('status', 'Active'))),

            'resigned' => Tab::make('Alumni / Resign')
                ->icon('heroicon-o-user-minus')
                ->badge(CareerMovement::whereHas('employee', fn ($q) => $q->where('status', 'Resigned'))->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('employee', fn ($q) => $q->where('status', 'Resigned'))),

            'all' => Tab::make('Semua Riwayat')
                ->icon('heroicon-o-clock')
                ->badge(CareerMovement::count())
                ->badgeColor('primary'),
        ];
    }
}
