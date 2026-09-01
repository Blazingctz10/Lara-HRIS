<?php

namespace App\Filament\Resources\FingerprintDevices\Pages;

use App\Filament\Resources\FingerprintDevices\FingerprintDeviceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFingerprintDevice extends EditRecord
{
    protected static string $resource = FingerprintDeviceResource::class;

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
