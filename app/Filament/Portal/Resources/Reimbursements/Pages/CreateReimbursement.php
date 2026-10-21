<?php

namespace App\Filament\Portal\Resources\Reimbursements\Pages;

use App\Filament\Portal\Resources\Reimbursements\ReimbursementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReimbursement extends CreateRecord
{
    protected static string $resource = ReimbursementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $employee = auth()->user()->employee;
        if (! $employee) {
            abort(403, 'Profil pegawai tidak ditemukan.');
        }

        $data['employee_id'] = $employee->id;
        $data['status'] = 'Pending';
        $data['payment_date'] = null;
        $data['approved_by'] = null;

        return $data;
    }

    protected function afterCreate(): void
    {
        try {
            $record = $this->record;
            $employee = $record->employee;
            $empName = $employee?->full_name ?? auth()->user()->name;
            $amountFormatted = 'Rp ' . number_format($record->amount, 0, ',', '.');

            $recipients = \App\Models\User::whereIn('role', ['hr_admin', 'super_admin'])->get();
            if ($recipients->isNotEmpty()) {
                \Filament\Notifications\Notification::make()
                    ->title('Pengajuan Reimbursement Baru')
                    ->body("{$empName} mengajukan klaim '{$record->title}' ({$amountFormatted}).")
                    ->icon('heroicon-o-receipt-percent')
                    ->iconColor('warning')
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view')
                            ->label('Lihat Klaim')
                            ->url('/admin/reimbursements'),
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
