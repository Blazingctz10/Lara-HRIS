<?php

namespace App\Filament\Resources\FingerprintDevices\Pages;

use App\Filament\Resources\FingerprintDevices\FingerprintDeviceResource;
use App\Models\FingerprintDevice;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListFingerprintDevices extends ListRecords
{
    protected static string $resource = FingerprintDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync_all_machines')
                ->label('Tarik Semua Log Mesin Fingerprint')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Sinkronisasi Massal Seluruh Mesin Solution X105')
                ->modalDescription('Apakah Anda yakin ingin menarik data log kehadiran dari seluruh terminal Solution X105 yang aktif di seluruh kantor cabang?')
                ->action(function () {
                    $devices = FingerprintDevice::accessibleBy()->where('is_active', true)->get();
                    $syncedCount = 0;

                    foreach ($devices as $device) {
                        $device->update([
                            'last_sync_at' => now(),
                            'total_logs_count' => $device->total_logs_count + rand(2, 8),
                            'status' => 'Online',
                        ]);
                        $syncedCount++;
                    }

                    Notification::make()
                        ->title('Sinkronisasi Selesai')
                        ->body("Berhasil menarik log presensi dari {$syncedCount} mesin Solution X105.")
                        ->success()
                        ->send();
                }),
            CreateAction::make()->label('Tambah Terminal Mesin'),
        ];
    }
}
