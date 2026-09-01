<?php

namespace App\Filament\Resources\Reimbursements\Pages;

use App\Filament\Resources\Reimbursements\ReimbursementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReimbursement extends EditRecord
{
    protected static string $resource = ReimbursementResource::class;

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
