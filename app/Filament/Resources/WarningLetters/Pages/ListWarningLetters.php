<?php

namespace App\Filament\Resources\WarningLetters\Pages;

use App\Filament\Resources\WarningLetters\WarningLetterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWarningLetters extends ListRecords
{
    protected static string $resource = WarningLetterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Terbitkan Surat Peringatan (SP)'),
        ];
    }
}
