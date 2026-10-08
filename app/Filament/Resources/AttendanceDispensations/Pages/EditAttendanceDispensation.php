<?php

namespace App\Filament\Resources\AttendanceDispensations\Pages;

use App\Filament\Resources\AttendanceDispensations\AttendanceDispensationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAttendanceDispensation extends EditRecord
{
    protected static string $resource = AttendanceDispensationResource::class;

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
