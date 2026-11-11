<?php

namespace App\Filament\Resources\CompliancePolicies\Pages;

use App\Filament\Resources\CompliancePolicies\CompliancePolicyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCompliancePolicy extends EditRecord
{
    protected static string $resource = CompliancePolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

