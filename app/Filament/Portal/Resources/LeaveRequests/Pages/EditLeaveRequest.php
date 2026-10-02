<?php

namespace App\Filament\Portal\Resources\LeaveRequests\Pages;

use App\Filament\Portal\Resources\LeaveRequests\LeaveRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLeaveRequest extends EditRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        if ($this->getRecord()->status !== 'Pending') {
            abort(403, 'Pengajuan cuti yang telah diproses tidak dapat diubah lagi.');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
