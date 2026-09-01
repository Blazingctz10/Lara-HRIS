<?php

namespace App\Filament\Portal\Resources\Overtimes\Pages;

use App\Filament\Portal\Resources\Overtimes\OvertimeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOvertimes extends ListRecords
{
    protected static string $resource = OvertimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Ajukan Lembur Baru'),
        ];
    }
}
