<?php

namespace App\Filament\Portal\Resources\CompliancePolicies\Pages;

use App\Filament\Portal\Resources\CompliancePolicies\CompliancePolicyResource;
use Filament\Resources\Pages\ListRecords;

class ListCompliancePolicies extends ListRecords
{
    protected static string $resource = CompliancePolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
