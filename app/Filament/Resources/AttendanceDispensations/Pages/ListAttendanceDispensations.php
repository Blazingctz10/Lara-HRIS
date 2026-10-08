<?php

namespace App\Filament\Resources\AttendanceDispensations\Pages;

use App\Filament\Resources\AttendanceDispensations\AttendanceDispensationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceDispensations extends ListRecords
{
    protected static string $resource = AttendanceDispensationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Catat Dispensasi Baru (HR/GA)'),
        ];
    }
}
