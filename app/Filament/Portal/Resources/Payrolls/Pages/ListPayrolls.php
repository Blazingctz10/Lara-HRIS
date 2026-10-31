<?php

namespace App\Filament\Portal\Resources\Payrolls\Pages;

use App\Filament\Portal\Resources\Payrolls\PayrollResource;
use Filament\Resources\Pages\ListRecords;

class ListPayrolls extends ListRecords
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
