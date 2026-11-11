<?php

namespace App\Filament\Resources\Overtimes\Tables;

use App\Models\Overtime;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OvertimesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['employee.branch']))
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('employee.branch.name')
                    ->label('Unit Kantor')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('start_time')
                    ->label('Mulai')
                    ->time('H:i'),

                TextColumn::make('end_time')
                    ->label('Selesai')
                    ->time('H:i'),

                TextColumn::make('duration_hours')
                    ->label('Durasi')
                    ->suffix(' Jam')
                    ->badge()
                    ->color('warning')
                    ->alignCenter(),

                TextColumn::make('reason')
                    ->label('Alasan / Kegiatan')
                    ->limit(35)
                    ->tooltip(fn (Overtime $record) => $record->reason),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Approved_HR', 'Approved', 'Approved_Pincab' => 'success',
                        'Rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Pending' => 'Menunggu Persetujuan HR',
                        'Approved_HR', 'Approved', 'Approved_Pincab' => 'Disetujui HR',
                        'Rejected' => 'Ditolak',
                        default => $state,
                    }),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui Lembur')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->button()
                    ->visible(fn (Overtime $record) => $record->status === 'Pending' && in_array(auth()->user()?->role, ['super_admin', 'hr_admin']))
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Persetujuan Lembur (SDM / HR)')
                    ->modalDescription(fn (Overtime $record) => "Setujui surat perintah tugas lembur pegawai {$record->employee->full_name} untuk tanggal " . \Carbon\Carbon::parse($record->date)->translatedFormat('d F Y') . " selama {$record->duration_hours} jam untuk perhitungan kompensasi payroll?")
                    ->modalSubmitActionLabel('Ya, Setujui Lembur')
                    ->modalCancelActionLabel('Batal')
                    ->modalIcon('heroicon-o-check-badge')
                    ->action(function (Overtime $record) {
                        $record->update([
                            'status' => 'Approved_HR',
                            'approved_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Lembur Disetujui HR')
                            ->body("Pengajuan lembur {$record->employee->full_name} berhasil disetujui.")
                            ->success()
                            ->send();

                        if ($record->employee?->user) {
                            Notification::make()
                                ->title('Pengajuan Lembur Disetujui HR')
                                ->body("Pengajuan lembur tanggal " . \Carbon\Carbon::parse($record->date)->format('d/m/Y') . " ({$record->duration_hours} jam) telah disetujui HR dan masuk ke komponen payroll.")
                                ->icon('heroicon-o-check-badge')
                                ->iconColor('success')
                                ->sendToDatabase($record->employee->user);
                        }
                    }),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->button()
                    ->outlined()
                    ->visible(fn (Overtime $record) => $record->status === 'Pending' && in_array(auth()->user()?->role, ['super_admin', 'hr_admin']))
                    ->form([
                        \Filament\Forms\Components\Textarea::make('rejection_note')
                            ->label('Alasan Penolakan')
                            ->placeholder('Jelaskan alasan penolakan pengajuan lembur ini...')
                            ->required(),
                    ])
                    ->modalHeading('Tolak Pengajuan Lembur')
                    ->modalSubmitActionLabel('Tolak Pengajuan')
                    ->modalCancelActionLabel('Batal')
                    ->modalIcon('heroicon-o-x-circle')
                    ->action(function (Overtime $record, array $data) {
                        $record->update([
                            'status' => 'Rejected',
                            'approved_by' => auth()->id(),
                            'rejection_note' => $data['rejection_note'] ?? null,
                        ]);

                        Notification::make()
                            ->title('Pengajuan Lembur Ditolak')
                            ->body("Pengajuan lembur {$record->employee->full_name} telah ditolak.")
                            ->danger()
                            ->send();

                        if ($record->employee?->user) {
                            Notification::make()
                                ->title('Pengajuan Lembur Ditolak HR')
                                ->body("Pengajuan lembur tanggal " . \Carbon\Carbon::parse($record->date)->format('d/m/Y') . " tidak disetujui HR: " . ($data['rejection_note'] ?? 'Tidak memenuhi syarat.'))
                                ->icon('heroicon-o-x-circle')
                                ->iconColor('danger')
                                ->sendToDatabase($record->employee->user);
                        }
                    }),

                EditAction::make()
                    ->button()
                    ->outlined()
                    ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
            ]);
    }
}
