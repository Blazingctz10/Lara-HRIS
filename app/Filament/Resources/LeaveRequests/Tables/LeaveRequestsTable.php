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
                        'Approved_HRD' => 'ACC HRD (Final Direksi ≤ 2 Hari)',
                        'Pending_Komisaris' => 'Menunggu Dewan Komisaris',
                        'Approved_Komisaris' => 'Disetujui Dewan Komisaris (Final)',
                        'Rejected' => 'Ditolak',
                    ]),
                SelectFilter::make('leave_type_id')
                    ->relationship('leaveType', 'name')
                    ->label('Jenis Cuti'),
            ])
            ->recordActions([
                // 1. Verifikasi HR untuk Karyawan Regular
                Action::make('approve_hr')
                    ->label('Verifikasi HR')
                    ->icon('heroicon-o-document-check')
                    ->color('primary')
                    ->button()
                    ->visible(fn (LeaveRequest $record) => ! $record->isDireksiRequest() && $record->status === 'Pending' && (auth()->user()->isHrAdmin() && !auth()->user()->isDireksi() || auth()->user()->isSuperAdmin()))
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
                            'approved_hrd_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Pengajuan cuti berhasil diverifikasi HR')
                            ->body('Status diteruskan ke Direksi untuk otorisasi final.')
                            ->success()
                            ->send();
                    }),

                // 2. Approval Direksi untuk Karyawan Regular
                Action::make('approve_direksi')
                    ->label('Approval Direksi (Final)')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->button()
                    ->visible(fn (LeaveRequest $record) => ! $record->isDireksiRequest() && $record->status === 'Approved_HR' && (auth()->user()->isDireksi() || auth()->user()->isSuperAdmin()))
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Persetujuan Final Cuti oleh Direksi')
                    ->modalDescription(fn (LeaveRequest $record) => "Persetujuan Direksi adalah otorisasi final yang secara resmi mengesahkan permohonan cuti {$record->employee->user->name} selama {$record->total_days} hari kerja dan memotong saldo cuti secara otomatis. Lanjutkan?")
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
                            ->body('Saldo cuti pegawai telah dipotong dan log presensi disinkronkan.')
                            ->success()
                            ->send();
                    }),

                // 3. KHUSUS DIREKSI: Cuti Singkat (<= 2 Hari) cukup di-ACC HRD
                Action::make('acc_hrd_direksi')
                    ->label('ACC HRD (Final Direksi ≤ 2 Hari)')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->button()
                    ->visible(fn (LeaveRequest $record) => $record->isDireksiRequest() && $record->total_days <= 2 && in_array($record->status, ['Pending', 'Pending_HRD']) && (auth()->user()->isHrAdmin() || auth()->user()->isSuperAdmin()))
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi ACC Cuti Direksi oleh HRD')
                    ->modalDescription(fn (LeaveRequest $record) => "Sesuai regulasi BPR Banda Raya, cuti singkat Direksi (≤ 2 hari) dapat disahkan langsung oleh HRD tanpa ke Dewan Komisaris. Lanjutkan pengesahan?")
                    ->modalSubmitActionLabel('Ya, ACC Cuti Direksi')
                    ->modalCancelActionLabel('Batal')
                    ->action(function (LeaveRequest $record) {
                        $record->update([
                            'status' => 'Approved_HRD',
                            'approved_by' => auth()->id(),
                            'approved_hrd_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Cuti Direksi Telah Disahkan oleh HRD')
                            ->body('Status cuti singkat disetujui resmi.')
                            ->success()
                            ->send();
                    }),

                // 4. KHUSUS DIREKSI: Cuti Lama (> 2 Hari) Tahap 1: Verifikasi HRD
                Action::make('rekomendasi_hrd_direksi')
                    ->label('Teruskan ke Dewan Komisaris')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->button()
                    ->visible(fn (LeaveRequest $record) => $record->isDireksiRequest() && $record->total_days > 2 && $record->status === 'Pending' && (auth()->user()->isHrAdmin() || auth()->user()->isSuperAdmin()))
                    ->requiresConfirmation()
                    ->modalHeading('Verifikasi Berkas Cuti Direksi (> 2 Hari)')
                    ->modalDescription('Cuti Direksi berdurasi lebih dari 2 hari kerja wajib diteruskan kepada Dewan Komisaris untuk persetujuan.')
                    ->modalSubmitActionLabel('Teruskan ke Komisaris')
                    ->action(function (LeaveRequest $record) {
                        $record->update([
                            'status' => 'Pending_Komisaris',
                            'approved_hrd_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Berkas Cuti Direksi Diteruskan ke Dewan Komisaris')
                            ->success()
                            ->send();
                    }),

                // 5. KHUSUS DIREKSI: Cuti Lama (> 2 Hari) Tahap 2: Persetujuan Dewan Komisaris
                Action::make('approve_komisaris')
                    ->label('Approval Komisaris (Final)')
                    ->icon('heroicon-o-sparkles')
                    ->color('success')
                    ->button()
                    ->visible(fn (LeaveRequest $record) => $record->status === 'Pending_Komisaris' && (auth()->user()->isSuperAdmin() || auth()->user()->role === 'komisaris' || auth()->user()->isHrAdmin()))
                    ->requiresConfirmation()
                    ->modalHeading('Persetujuan Dewan Komisaris atas Cuti Direksi')
                    ->modalDescription(fn (LeaveRequest $record) => "Otorisasi final oleh Dewan Komisaris atas permohonan cuti {$record->employee->user->name} selama {$record->total_days} hari kerja.")
                    ->form([
                        Textarea::make('komisaris_notes')
                            ->label('Catatan / Keputusan Dewan Komisaris')
                            ->placeholder('Contoh: Disetujui sesuai Keputusan Rapat Dewan Komisaris No. ...')
                            ->nullable(),
                    ])
                    ->action(function (LeaveRequest $record, array $data) {
                        $record->update([
                            'status' => 'Approved_Komisaris',
                            'approved_by' => auth()->id(),
                            'approved_komisaris_by' => auth()->id(),
                            'komisaris_notes' => $data['komisaris_notes'] ?? null,
                        ]);

                        Notification::make()
                            ->title('Cuti Direksi Resmi Disetujui Dewan Komisaris')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->button()
                    ->visible(fn (LeaveRequest $record) => in_array($record->status, ['Pending', 'Approved_HR', 'Pending_Komisaris']) && (auth()->user()->isHrAdmin() || auth()->user()->isDireksi() || auth()->user()->isSuperAdmin()))
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

                Action::make('print_form')
                    ->label('Cetak Formulir')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->button()
                    ->outlined()
                    ->url(fn (LeaveRequest $record) => route('leave-requests.print', $record))
                    ->openUrlInNewTab(),

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
