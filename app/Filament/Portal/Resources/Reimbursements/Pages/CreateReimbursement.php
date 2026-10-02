<?php

namespace App\Filament\Portal\Resources\Reimbursements\Pages;

use App\Filament\Portal\Resources\Reimbursements\ReimbursementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReimbursement extends CreateRecord
{
    protected static string $resource = ReimbursementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $employee = auth()->user()->employee;
        if (! $employee) {
            abort(403, 'Profil pegawai tidak ditemukan.');
        }

        $data['employee_id'] = $employee->id;
        $data['status'] = 'Pending';
        $data['payment_date'] = null;
        $data['approved_by'] = null;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
