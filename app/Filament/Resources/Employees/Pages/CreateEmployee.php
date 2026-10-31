<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    public function mount(): void
    {
        if (auth()->user()?->role === 'direksi') {
            abort(403, 'Akses ditolak. Akun Direksi hanya memiliki hak pengawasan (Read-Only) data pegawai.');
        }

        parent::mount();
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Pendaftaran Pegawai Baru')
            ->modalDescription('Pastikan data identitas KTP, NIP, kantor penempatan, dan akun masuk pegawai sudah lengkap dan akurat sebelum disimpan.')
            ->modalSubmitActionLabel('Ya, Daftarkan Pegawai')
            ->modalCancelActionLabel('Batal');
    }

    protected function afterCreate(): void
    {
        if ($this->record->email && ! $this->record->user_id) {
            $user = User::firstOrCreate(
                ['email' => $this->record->email],
                [
                    'name' => $this->record->full_name,
                    'password' => Hash::make('password'),
                    'role' => 'employee',
                    'branch_id' => $this->record->branch_id,
                ]
            );
            $this->record->update(['user_id' => $user->id]);
        }
    }
}
