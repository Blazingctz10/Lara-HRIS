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
            Action::make('go_to_upload')
                ->label('Tarik & Unggah Berkas Presensi')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->url(url('/admin/attendances')),
            CreateAction::make()->label('Tambah Terminal Mesin'),
        ];
    }
}
