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

                TextColumn::make('total_logs_count')
                    ->label('Total Log Terdaftar')
                    ->suffix(' Data')
                    ->alignCenter(),

                IconColumn::make('is_active')
                    ->label('Terminal Aktif')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->recordActions([
                Action::make('upload_log')
                    ->label('Unggah Log Mesin')
                    ->icon('heroicon-m-arrow-up-tray')
                    ->color('primary')
                    ->url(url('/admin/attendances')),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
