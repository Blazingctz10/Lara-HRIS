<?php

namespace App\Filament\Resources\CompliancePolicies\Pages;

use App\Filament\Resources\CompliancePolicies\CompliancePolicyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompliancePolicies extends ListRecords
{
    protected static string $resource = CompliancePolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Unggah Kebijakan Baru')
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}

