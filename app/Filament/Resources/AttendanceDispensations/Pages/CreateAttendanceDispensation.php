<?php

namespace App\Filament\Resources\AttendanceDispensations\Pages;

use App\Filament\Resources\AttendanceDispensations\AttendanceDispensationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendanceDispensation extends CreateRecord
{
    protected static string $resource = AttendanceDispensationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
