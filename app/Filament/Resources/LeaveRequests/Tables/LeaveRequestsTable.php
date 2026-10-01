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
                        'Approved_HR' => 'success',
                        'Approved_Pincab' => 'info',
                        'Pending' => 'warning',
                        'Rejected' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('approver.name')
                    ->label('Disetujui Oleh')
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Pending' => 'Pending',
                        'Approved_Pincab' => 'Approved Pincab',
                        'Approved_HR' => 'Approved HR',
                        'Rejected' => 'Rejected',
                    ]),
                SelectFilter::make('leave_type_id')
                    ->relationship('leaveType', 'name')
                    ->label('Jenis Cuti'),
            ])
            ->recordActions([
                Action::make('approve_pincab')
                    ->label('Setujui Cabang')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->visible(fn (LeaveRequest $record) => $record->status === 'Pending' && auth()->user()->isBranchManager())
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Persetujuan Cuti (Pincab)')
                    ->modalDescription(fn (LeaveRequest $record) => "Apakah Anda yakin ingin menyetujui permohonan cuti untuk {$record->employee->user->name} selama {$record->total_days} hari?")
                    ->modalSubmitActionLabel('Ya, Setujui Cuti')
                    ->modalCancelActionLabel('Batal')
                    ->action(function (LeaveRequest $record) {
                        $record->update([
                            'status' => 'Approved_Pincab',
                            'approved_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Pengajuan cuti disetujui Kepala Cabang')
                            ->success()
                            ->send();
                    }),

                Action::make('approve_hr')
                    ->label('Final Approve HR')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (LeaveRequest $record) => in_array($record->status, ['Pending', 'Approved_Pincab']) && auth()->user()->isHrAdmin())
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Persetujuan Final Cuti (HR)')
                    ->modalDescription(fn (LeaveRequest $record) => "Persetujuan final ini akan secara otomatis memotong {$record->total_days} hari dari saldo cuti resmi pegawai {$record->employee->user->name}. Lanjutkan?")
                    ->modalSubmitActionLabel('Ya, Setujui Final')
                    ->modalCancelActionLabel('Batal')
                    ->action(function (LeaveRequest $record) {
                        $record->update([
                            'status' => 'Approved_HR',
                            'approved_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Pengajuan cuti berhasil disetujui Final oleh HR')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (LeaveRequest $record) => in_array($record->status, ['Pending', 'Approved_Pincab']))
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Penolakan Pengajuan Cuti')
                    ->modalDescription('Mohon isi alasan penolakan sebelum memproses pembatalan pengajuan cuti ini.')
                    ->modalSubmitActionLabel('Tolak Pengajuan')
                    ->modalCancelActionLabel('Batal')
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

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
