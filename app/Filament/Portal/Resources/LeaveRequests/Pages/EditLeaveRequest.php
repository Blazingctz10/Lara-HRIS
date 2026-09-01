<?php

namespace App\Filament\Portal\Resources\LeaveRequests\Pages;

use App\Filament\Portal\Resources\LeaveRequests\LeaveRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLeaveRequest extends EditRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
