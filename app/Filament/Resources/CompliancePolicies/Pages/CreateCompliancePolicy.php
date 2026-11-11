<?php

namespace App\Filament\Resources\CompliancePolicies\Pages;

use App\Filament\Resources\CompliancePolicies\CompliancePolicyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCompliancePolicy extends CreateRecord
{
    protected static string $resource = CompliancePolicyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}

