<?php

namespace App\Filament\Portal\Resources\Reimbursements\Pages;

use App\Filament\Portal\Resources\Reimbursements\ReimbursementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReimbursements extends ListRecords
{
    protected static string $resource = ReimbursementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Ajukan Klaim Reimbursement Baru')
                ->url(fn (): string => static::getResource()::getUrl('create')),
        ];
    }
}
