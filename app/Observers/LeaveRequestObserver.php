<?php

namespace App\Observers;

use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeaveRequestObserver
{
    /**
     * Handle the LeaveRequest "created" event.
     */
    public function created(LeaveRequest $leaveRequest): void
    {
        try {
            $employeeName = $leaveRequest->employee?->full_name ?? $leaveRequest->employee?->user?->name ?? 'Pegawai';
            $leaveTypeName = $leaveRequest->leaveType?->name ?? 'Cuti';
            
            // Penerima notifikasi: seluruh HR Admin & Super Admin
            $recipients = \App\Models\User::whereIn('role', ['hr_admin', 'super_admin'])->get();

            if ($recipients->isNotEmpty()) {
                \Filament\Notifications\Notification::make()
                    ->title('Pengajuan Cuti Baru')
                    ->body("{$employeeName} mengajukan {$leaveTypeName} ({$leaveRequest->total_days} hari kerja). Menunggu verifikasi berkas.")
                    ->icon('heroicon-o-document-text')
                    ->iconColor('warning')
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view')
                            ->label('Buka Daftar Cuti')
                            ->url('/admin/leave-requests'),
                    ])
                    ->sendToDatabase($recipients);
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal kirim notifikasi pengajuan cuti baru: ' . $e->getMessage());
        }
    }

    /**
     * Handle the LeaveRequest "updated" event.
     */
    public function updated(LeaveRequest $leaveRequest): void
    {
        if ($leaveRequest->wasChanged('status')) {
            $originalStatus = $leaveRequest->getOriginal('status');
            $newStatus = $leaveRequest->status;
            $year = Carbon::parse($leaveRequest->start_date)->year;

            $finalStatuses = ['Approved_Direksi', 'Approved_HRD', 'Approved_Komisaris', 'Approved_HR'];
            $isFinalApproved = in_array($newStatus, $finalStatuses);
            $wasFinalApproved = in_array($originalStatus, $finalStatuses);

            // Notifikasi ke Direksi ketika status berubah menjadi Approved_HR (siap otorisasi final)
            if ($newStatus === 'Approved_HR') {
                try {
                    $direksiUsers = \App\Models\User::where('role', 'direksi')->get();
                    if ($direksiUsers->isNotEmpty()) {
                        $empName = $leaveRequest->employee?->full_name ?? $leaveRequest->employee?->user?->name ?? 'Pegawai';
                        \Filament\Notifications\Notification::make()
                            ->title('Otorisasi Final Cuti Direksi')
                            ->body("Pengajuan cuti {$empName} telah diverifikasi HRD dan menunggu persetujuan akhir Direksi.")
                            ->icon('heroicon-o-check-badge')
                            ->iconColor('primary')
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('view')
                                    ->label('Otorisasi Sekarang')
                                    ->url('/admin/leave-requests'),
                            ])
                            ->sendToDatabase($direksiUsers);
                    }
                } catch (\Throwable $e) {
                    Log::warning('Gagal kirim notifikasi cuti ke direksi: ' . $e->getMessage());
                }
            }

            // Notifikasi balik ke Pegawai yang mengajukan jika status disetujui atau ditolak
            if ($leaveRequest->employee?->user && in_array($newStatus, ['Approved_Direksi', 'Approved_HRD', 'Approved_Komisaris', 'Rejected'])) {
                try {
                    $isApproved = $newStatus !== 'Rejected';
                    $statusText = $isApproved ? 'Telah Disetujui Resmi' : 'Ditolak: ' . ($leaveRequest->rejection_reason ?? 'Tidak memenuhi syarat');
                    $ltName = $leaveRequest->leaveType?->name ?? 'Cuti';

                    \Filament\Notifications\Notification::make()
                        ->title("Permohonan Cuti {$statusText}")
                        ->body("Permohonan {$ltName} Anda telah diputuskan.")
                        ->icon($isApproved ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                        ->iconColor($isApproved ? 'success' : 'danger')
                        ->sendToDatabase($leaveRequest->employee->user);
                } catch (\Throwable $e) {
                    Log::warning('Gagal kirim notifikasi status cuti ke pegawai: ' . $e->getMessage());
                }
            }

            // Ketika disetujui final oleh Direksi, HRD, atau Komisaris, potong saldo cuti resmi & sinkronkan presensi
            if ($isFinalApproved && ! $wasFinalApproved) {
                DB::transaction(function () use ($leaveRequest, $year) {
                    $balance = EmployeeLeaveBalance::firstOrCreate(
                        [
                            'employee_id' => $leaveRequest->employee_id,
                            'leave_type_id' => $leaveRequest->leave_type_id,
                            'year' => $year,
                        ],
                        [
                            'initial_quota' => $leaveRequest->leaveType->default_days ?? 12,
                            'used_quota' => 0,
                            'remaining_quota' => $leaveRequest->leaveType->default_days ?? 12,
                        ]
                    );

                    $balance->used_quota += $leaveRequest->total_days;
                    $balance->remaining_quota = max(0, $balance->initial_quota - $balance->used_quota);
                    $balance->save();

                    // Sinkronisasi otomatis ke log kehadiran (status = Excused)
                    $startDate = Carbon::parse($leaveRequest->start_date);
                    $endDate = Carbon::parse($leaveRequest->end_date);
                    for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
                        if (! $d->isSunday()) {
                            \App\Models\Attendance::updateOrCreate(
                                [
                                    'employee_id' => $leaveRequest->employee_id,
                                    'date' => $d->toDateString(),
                                ],
                                [
                                    'status' => 'Excused',
                                    'notes' => '[Cuti Resmi: ' . ($leaveRequest->leaveType?->name ?? 'Cuti') . '] ' . $leaveRequest->reason,
                                ]
                            );
                        }
                    }

                    Log::info("Leave balance deducted and attendance synced for employee {$leaveRequest->employee_id}: {$leaveRequest->total_days} days. Remaining: {$balance->remaining_quota}");
                });
            }

            // Jika status yang sudah disetujui dibatalkan/ditolak, kembalikan kuota saldo cuti
            if ($wasFinalApproved && ! $isFinalApproved) {
                DB::transaction(function () use ($leaveRequest, $year) {
                    $balance = EmployeeLeaveBalance::where([
                        'employee_id' => $leaveRequest->employee_id,
                        'leave_type_id' => $leaveRequest->leave_type_id,
                        'year' => $year,
                    ])->first();

                    if ($balance) {
                        $balance->used_quota = max(0, $balance->used_quota - $leaveRequest->total_days);
                        $balance->remaining_quota = min($balance->initial_quota, $balance->initial_quota - $balance->used_quota);
                        $balance->save();
                    }
                });
            }
        }
    }
}
