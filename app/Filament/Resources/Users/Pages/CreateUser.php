<?php

namespace App\Filament\Resources\Users\Pages;

use App\Models\Employee;
use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $employeeId = $this->data['employee_id'] ?? null;
        if ($employeeId) {
            Employee::where('id', $employeeId)->update(['user_id' => $this->record->id]);
        } else {
            Employee::where('email', $this->record->email)->whereNull('user_id')->update(['user_id' => $this->record->id]);
        }
    }
}
