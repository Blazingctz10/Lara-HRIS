<?php

namespace App\Filament\Resources\Users\Pages;

use App\Models\Employee;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn ($record) => $record->id === auth()->id()),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['employee_id'] = $this->record->employee?->id;
        return $data;
    }

    protected function afterSave(): void
    {
        $employeeId = $this->data['employee_id'] ?? null;
        if ($employeeId) {
            Employee::where('user_id', $this->record->id)
                ->where('id', '!=', $employeeId)
                ->update(['user_id' => null]);

            Employee::where('id', $employeeId)->update(['user_id' => $this->record->id]);
        }
    }
}
