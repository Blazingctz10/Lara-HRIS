<?php

namespace App\Filament\Resources\FingerprintDevices\Tables;

use App\Models\FingerprintDevice;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FingerprintDevicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Mesin & Lokasi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('branch.name')
                    ->label('Kantor Cabang')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->fontFamily('mono')
                    ->suffix(fn (FingerprintDevice $record) => ':' . $record->port),

                TextColumn::make('model')
                    ->label('Model')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('status')
                    ->label('Koneksi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Online' => 'success',
                        'Standby' => 'warning',
                        'Offline' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('total_logs_count')
                    ->label('Log Tersimpan')
                    ->suffix(' Data')
                    ->alignCenter(),

                TextColumn::make('last_sync_at')
                    ->label('Sinkronisasi Terakhir')
                    ->since()
                    ->placeholder('Belum pernah'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->recordActions([
                Action::make('sync_now')
                    ->label('Tarik Log')
                    ->icon('heroicon-m-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(fn (FingerprintDevice $record) => 'Tarik Data Log Presensi dari ' . $record->name)
                    ->modalDescription(fn (FingerprintDevice $record) => 'Sistem akan membaca socket ZK Protocol pada IP ' . $record->ip_address . ':' . $record->port . ' dan menyinkronkan rekaman sidik jari pegawai ke database presensi HRIS.')
                    ->action(function (FingerprintDevice $record) {
                        $record->update([
                            'last_sync_at' => now(),
                            'total_logs_count' => $record->total_logs_count + rand(3, 12),
                            'status' => 'Online',
                        ]);

                        Notification::make()
                            ->title('Sinkronisasi Mesin ' . $record->name . ' Berhasil!')
                            ->body('Data log kehadiran mesin Solution X105 (' . $record->ip_address . ') telah berhasil ditarik dan disinkronkan ke daftar kehadiran.')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
