<?php

namespace App\Filament\Portal\Resources\LeaveRequests\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tgl Pengajuan')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('leaveType.name')
                    ->label('Jenis Cuti')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Selesai')
                    ->date('d M Y'),

                TextColumn::make('total_days')
                    ->label('Durasi')
                    ->suffix(' Hari')
                    ->alignCenter()
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label('Status Approval')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Approved_Direksi' => 'success',
                        'Approved_HR' => 'primary',
                        'Approved_Pincab' => 'info',
                        'Pending' => 'warning',
                        'Rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Approved_Direksi' => 'Disetujui Direksi (Final)',
                        'Approved_HR' => 'Verifikasi HR',
                        'Approved_Pincab' => 'Persetujuan Cabang',
                        'Pending' => 'Menunggu Approval',
                        'Rejected' => 'Ditolak',
                        default => $state,
                    }),

                TextColumn::make('approver.name')
                    ->label('Disetujui Oleh')
                    ->placeholder('-'),

                TextColumn::make('rejection_reason')
                    ->label('Catatan Penolakan')
                    ->placeholder('-')
                    ->color('danger')
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->visible(fn ($record) => $record->status === 'Pending'),
            ]);
    }
}
