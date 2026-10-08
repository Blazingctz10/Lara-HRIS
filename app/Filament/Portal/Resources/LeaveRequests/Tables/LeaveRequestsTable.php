<?php

namespace App\Filament\Portal\Resources\LeaveRequests\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['leaveType']))
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
                        'Approved_Direksi', 'Approved_HRD', 'Approved_Komisaris' => 'success',
                        'Approved_HR' => 'primary',
                        'Pending', 'Pending_Komisaris' => 'warning',
                        'Rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Approved_Direksi' => 'Disetujui Direksi (Final)',
                        'Approved_HRD' => 'ACC HRD (Final Direksi ≤ 2 Hari)',
                        'Approved_Komisaris' => 'Disetujui Dewan Komisaris (Final)',
                        'Pending_Komisaris' => 'Menunggu Dewan Komisaris',
                        'Approved_HR' => 'Menunggu Otorisasi Direksi (Verifikasi HR OK)',
                        'Pending' => 'Menunggu Verifikasi HR',
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
                Action::make('print_form')
                    ->label('Cetak Formulir')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->button()
                    ->outlined()
                    ->url(fn (\App\Models\LeaveRequest $record) => route('leave-requests.print', $record))
                    ->openUrlInNewTab(),

                EditAction::make()->visible(fn ($record) => $record->status === 'Pending'),
            ]);
    }
}
