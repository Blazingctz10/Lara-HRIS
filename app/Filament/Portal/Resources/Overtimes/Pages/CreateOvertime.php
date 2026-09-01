<?php

namespace App\Filament\Portal\Resources\Overtimes\Pages;

use App\Filament\Portal\Resources\Overtimes\OvertimeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOvertime extends CreateRecord
{
    protected static string $resource = OvertimeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
