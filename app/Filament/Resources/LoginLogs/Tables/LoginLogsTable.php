<?php

namespace App\Filament\Resources\LoginLogs\Tables;

use App\Models\LoginLog;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LoginLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['user']))
            ->defaultSort('login_at', 'desc')
            ->columns([
                TextColumn::make('login_at')
                    ->label('Waktu Login')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email Percobaan')
                    ->searchable()
                    ->copyable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Nama Akun Terdaftar')
                    ->placeholder('Tidak Terdaftar / Anonim')
                    ->searchable(),

                TextColumn::make('user.role')
                    ->label('Role Akun')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'direksi' => 'success',
                        'hr_admin' => 'primary',
                        'branch_manager' => 'info',
                        'employee' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'super_admin' => 'IT Admin',
                        'direksi' => 'Direksi',
                        'hr_admin' => 'HR Admin',
                        'branch_manager' => 'Pincab',
                        'employee' => 'Karyawan',
                        default => '-',
                    }),

                TextColumn::make('status')
                    ->label('Status Login')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Success' => 'success',
                        'Failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Success' => 'Berhasil Masuk',
                        'Failed' => 'Gagal / Ditolak',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('failure_reason')
                    ->label('Alasan / Keterangan')
                    ->placeholder('Otentikasi Berhasil')
                    ->badge()
                    ->color(fn (?string $state): string => $state ? 'warning' : 'success')
                    ->searchable(),

                TextColumn::make('ip_address')
                    ->label('Alamat IP')
                    ->fontFamily('mono')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('user_agent')
                    ->label('Perangkat / User Agent')
                    ->limit(45)
                    ->tooltip(fn (LoginLog $record): ?string => $record->user_agent)
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'Success' => 'Berhasil Masuk',
                        'Failed' => 'Gagal Masuk',
                    ]),
            ]);
    }
}
