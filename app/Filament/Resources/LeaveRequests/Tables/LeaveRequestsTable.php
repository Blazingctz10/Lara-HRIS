<?php

namespace App\Filament\Resources\LeaveRequests\Tables;

use App\Models\LeaveRequest;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['employee.branch', 'leaveType']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tgl Pengajuan')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('employee.nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('employee.full_name')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('employee.branch.name')
                    ->label('Cabang')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('leaveType.name')
                    ->label('Jenis Cuti')
                    ->badge()
                    ->color('primary')
                    ->searchable(),

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

                TextColumn::make('approver.name')
                    ->label('Disetujui Oleh')
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Pending' => 'Menunggu Verifikasi HR',
                        'Approved_HR' => 'Menunggu Otorisasi Direksi',
                        'Approved_Direksi' => 'Disetujui Direksi (Final)',
                        'Rejected' => 'Ditolak',
                    ]),
                SelectFilter::make('leave_type_id')
                    ->relationship('leaveType', 'name')
                    ->label('Jenis Cuti'),
            ])
            ->recordActions([
                Action::make('approve_hr')
                    ->label('Verifikasi HR')
                    ->icon('heroicon-o-document-check')
                    ->color('primary')
                    ->button()
                    ->visible(fn (LeaveRequest $record) => $record->status === 'Pending' && (auth()->user()->isHrAdmin() && !auth()->user()->isDireksi() || auth()->user()->isSuperAdmin()))
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Verifikasi Berkas Cuti (HR)')
                    ->modalDescription(fn (LeaveRequest $record) => "Verifikasi kelayakan berkas cuti untuk pegawai {$record->employee->user->name} sebelum diteruskan ke Direksi untuk persetujuan akhir.")
                    ->modalSubmitActionLabel('Ya, Verifikasi Berkas')
                    ->modalCancelActionLabel('Batal')
                    ->modalIcon('heroicon-o-document-check')
                    ->action(function (LeaveRequest $record) {
                        $record->update([
                            'status' => 'Approved_HR',
                            'approved_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Pengajuan cuti berhasil diverifikasi HR')
                            ->body('Status diteruskan ke Direksi untuk otorisasi final.')
                            ->success()
                            ->send();
                    }),

                Action::make('approve_direksi')
                    ->label('Approval Direksi (Final)')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->button()
                    ->visible(fn (LeaveRequest $record) => $record->status === 'Approved_HR' && (auth()->user()->isDireksi() || auth()->user()->isSuperAdmin()))
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Persetujuan Final Cuti oleh Direksi')
                    ->modalDescription(fn (LeaveRequest $record) => "Persetujuan Direksi adalah otorisasi final yang secara resmi mengesahkan permohonan cuti {$record->employee->user->name} selama {$record->total_days} hari kerja dan memotong saldo cuti tahun buku {$record->start_date->year} secara otomatis tanpa perlu hardcopy. Lanjutkan?")
                    ->modalSubmitActionLabel('Ya, Setujui Direksi (Final)')
                    ->modalCancelActionLabel('Batal')
                    ->modalIcon('heroicon-o-check-badge')
                    ->action(function (LeaveRequest $record) {
                        $record->update([
                            'status' => 'Approved_Direksi',
                            'approved_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Pengajuan cuti disetujui Final oleh Direksi')
                            ->body('Saldo cuti pegawai telah dipotong resmi secara digital.')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->button()
                    ->visible(fn (LeaveRequest $record) => in_array($record->status, ['Pending', 'Approved_HR']) && (auth()->user()->isHrAdmin() || auth()->user()->isDireksi() || auth()->user()->isSuperAdmin()))
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Penolakan Pengajuan Cuti')
                    ->modalDescription('Mohon isi alasan penolakan sebelum memproses pembatalan pengajuan cuti ini.')
                    ->modalSubmitActionLabel('Tolak Pengajuan')
                    ->modalCancelActionLabel('Batal')
                    ->modalIcon('heroicon-o-x-circle')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->required(),
                    ])
                    ->action(function (LeaveRequest $record, array $data) {
                        $record->update([
                            'status' => 'Rejected',
                            'approved_by' => auth()->id(),
                            'rejection_reason' => $data['rejection_reason'],
                        ]);

                        Notification::make()
                            ->title('Pengajuan cuti telah ditolak')
                            ->danger()
                            ->send();
                    }),

                EditAction::make()
                    ->button()
                    ->outlined(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
