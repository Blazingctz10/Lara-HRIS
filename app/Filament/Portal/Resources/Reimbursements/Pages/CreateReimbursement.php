<?php

namespace App\Filament\Portal\Resources\Reimbursements\Pages;

use App\Filament\Portal\Resources\Reimbursements\ReimbursementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReimbursement extends CreateRecord
{
    protected static string $resource = ReimbursementResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
