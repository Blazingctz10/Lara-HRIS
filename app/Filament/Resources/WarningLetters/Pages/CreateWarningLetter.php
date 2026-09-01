<?php

namespace App\Filament\Resources\WarningLetters\Pages;

use App\Filament\Resources\WarningLetters\WarningLetterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWarningLetter extends CreateRecord
{
    protected static string $resource = WarningLetterResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
