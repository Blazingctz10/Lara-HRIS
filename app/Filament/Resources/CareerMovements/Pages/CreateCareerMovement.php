<?php

namespace App\Filament\Resources\CareerMovements\Pages;

use App\Filament\Resources\CareerMovements\CareerMovementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCareerMovement extends CreateRecord
{
    protected static string $resource = CareerMovementResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
