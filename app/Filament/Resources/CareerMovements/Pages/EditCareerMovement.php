<?php

namespace App\Filament\Resources\CareerMovements\Pages;

use App\Filament\Resources\CareerMovements\CareerMovementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCareerMovement extends EditRecord
{
    protected static string $resource = CareerMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
