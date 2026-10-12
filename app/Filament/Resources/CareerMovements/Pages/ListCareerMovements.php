<?php

namespace App\Filament\Resources\CareerMovements\Pages;

use App\Filament\Resources\CareerMovements\CareerMovementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

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
}
