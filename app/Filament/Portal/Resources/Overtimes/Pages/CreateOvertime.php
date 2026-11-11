<?php

namespace App\Filament\Portal\Resources\Overtimes\Pages;

use App\Filament\Portal\Resources\Overtimes\OvertimeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOvertime extends CreateRecord
{
    protected static string $resource = OvertimeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $employee = auth()->user()->employee;
        if (! $employee) {
            abort(403, 'Profil pegawai tidak ditemukan.');
        }

        $data['employee_id'] = $employee->id;
        $data['status'] = 'Pending';
        $data['approved_by'] = null;

        return $data;
    }

    protected function afterCreate(): void
    {
        try {
            $record = $this->record;
            $employee = $record->employee;
            $empName = $employee?->full_name ?? auth()->user()->name;

            $recipients = \App\Models\User::whereIn('role', ['hr_admin', 'super_admin'])->get();
            if ($recipients->isNotEmpty()) {
                \Filament\Notifications\Notification::make()
                    ->title('Pengajuan Lembur Baru')
                    ->body("{$empName} mengajukan lembur {$record->duration_hours} jam (" . \Carbon\Carbon::parse($record->date)->format('d/m/Y') . ').')
                    ->icon('heroicon-o-fire')
                    ->iconColor('warning')
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view')
                            ->label('Lihat Pengajuan')
                            ->url('/admin/overtimes'),
                    ])
                    ->sendToDatabase($recipients);
            }
        } catch (\Throwable $e) {
            // ignore notification failure
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
