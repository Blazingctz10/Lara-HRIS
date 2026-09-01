<?php

namespace App\Filament\Resources\WarningLetters\Pages;

use App\Filament\Resources\WarningLetters\WarningLetterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWarningLetter extends EditRecord
{
    protected static string $resource = WarningLetterResource::class;

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
