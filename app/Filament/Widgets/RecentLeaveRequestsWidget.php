<?php

namespace App\Filament\Widgets;

use App\Models\LeaveRequest;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentLeaveRequestsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                LeaveRequest::accessibleBy()
                    ->whereIn('status', ['Pending', 'Approved_HR'])
                    ->latest()
            )
            ->heading('Antrean Pengajuan Cuti Menunggu Persetujuan')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y'),

                TextColumn::make('employee.full_name')
                    ->label('Nama Pegawai')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('employee.branch.name')
                    ->label('Cabang')
                    ->badge()
                    ->color('info'),

                TextColumn::make('leaveType.name')
                    ->label('Jenis Cuti')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date('d M Y'),

                TextColumn::make('end_date')
                    ->label('Selesai')
                    ->date('d M Y'),

                TextColumn::make('total_days')
                    ->label('Durasi')
                    ->suffix(' Hari')
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Approved_Direksi' => 'success',
                        'Approved_HR' => 'primary',
                        'Pending' => 'warning',
                        'Rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Approved_Direksi' => 'Disetujui Direksi (Final)',
                        'Approved_HR' => 'Menunggu Otorisasi Direksi',
                        'Pending' => 'Menunggu Verifikasi HR',
                        'Rejected' => 'Ditolak',
                        default => $state,
                    }),
            ])
            ->recordActions([
                Action::make('approve_hr')
                    ->label('Verifikasi HR')
                    ->icon('heroicon-o-document-check')
                    ->color('primary')
                    ->visible(fn (LeaveRequest $record) => $record->status === 'Pending' && (auth()->user()->isHrAdmin() && !auth()->user()->isDireksi() || auth()->user()->isSuperAdmin()))
                    ->requiresConfirmation()
                    ->action(function (LeaveRequest $record) {
                        $record->update([
                            'status' => 'Approved_HR',
                            'approved_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Diverifikasi oleh HR')
                            ->body('Status diteruskan ke Direksi untuk otorisasi final.')
                            ->success()
                            ->send();
                    }),

                Action::make('approve_direksi')
                    ->label('Approve Direksi (Final)')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (LeaveRequest $record) => $record->status === 'Approved_HR' && (auth()->user()->isDireksi() || auth()->user()->isSuperAdmin()))
                    ->requiresConfirmation()
                    ->action(function (LeaveRequest $record) {
                        $record->update([
                            'status' => 'Approved_Direksi',
                            'approved_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Cuti berhasil disetujui Final oleh Direksi')
                            ->body('Saldo cuti resmi pegawai telah dipotong digital.')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
