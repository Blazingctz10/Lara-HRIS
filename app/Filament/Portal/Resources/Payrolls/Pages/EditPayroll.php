<?php

namespace App\Filament\Portal\Resources\Payrolls\Pages;

use App\Filament\Portal\Resources\Payrolls\PayrollResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPayroll extends EditRecord
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
