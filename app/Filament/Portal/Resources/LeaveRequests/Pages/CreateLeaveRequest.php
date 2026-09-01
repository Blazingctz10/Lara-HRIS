<?php

namespace App\Filament\Portal\Resources\LeaveRequests\Pages;

use App\Filament\Portal\Resources\LeaveRequests\LeaveRequestResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateLeaveRequest extends CreateRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $employee = auth()->user()->employee;
        if (! $employee) {
            Notification::make()->title('Profil pegawai tidak ditemukan')->danger()->send();
            $this->halt();
        }

        $data['employee_id'] = $employee->id;
        $data['status'] = 'Pending';
        $data['approved_by'] = null;
        $data['rejection_reason'] = null;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
