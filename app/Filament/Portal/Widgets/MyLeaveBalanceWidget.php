<?php

namespace App\Filament\Portal\Widgets;

use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MyLeaveBalanceWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (! $employee) {
            return [
                Stat::make('Profil Pegawai', 'Belum Terhubung')
                    ->description('Hubungi HR untuk menautkan akun')
                    ->color('warning'),
            ];
        }

        $balances = EmployeeLeaveBalance::with('leaveType')
            ->where('employee_id', $employee->id)
            ->where('year', now()->year)
            ->get();

        $annualBalance = $balances->firstWhere('leaveType.code', 'ANNUAL');
        $sickBalance = $balances->firstWhere('leaveType.code', 'SICK');
        $blockBalance = $balances->firstWhere('leaveType.code', 'BLOCK_LEAVE');

        $pendingCount = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'Pending')
            ->count();

        $recentLeave = LeaveRequest::with('leaveType')
            ->where('employee_id', $employee->id)
            ->latest('start_date')
            ->first();

        $annualRemaining = $annualBalance->remaining_quota ?? 12;
        $annualUsed = $annualBalance->used_quota ?? 0;

        $sickRemaining = $sickBalance->remaining_quota ?? 14;
        $sickUsed = $sickBalance->used_quota ?? 0;

        $blockRemaining = $blockBalance->remaining_quota ?? 5;

        $stats = [
            Stat::make('Sisa Cuti Tahunan', "{$annualRemaining} Hari")
                ->description("Terpakai: {$annualUsed} hari • Kuota " . ($annualBalance->initial_quota ?? 12) . " hari")
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success')
                ->url('/portal/leave-requests'),

            Stat::make('Sisa Cuti Sakit', "{$sickRemaining} Hari")
                ->description("Terpakai: {$sickUsed} hari • Kuota " . ($sickBalance->initial_quota ?? 14) . " hari")
                ->descriptionIcon('heroicon-m-heart')
                ->color($sickUsed > 0 ? 'warning' : 'info')
                ->url('/portal/leave-requests'),

            Stat::make('Block Leave OJK', "{$blockRemaining} Hari Tersedia")
                ->description('Mandatori Bank • 5 hari kerja berurutan')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('primary')
                ->url('/portal/leave-requests/create'),
        ];

        if ($pendingCount > 0) {
            $stats[] = Stat::make('Pengajuan Berjalan', "{$pendingCount} Menunggu")
                ->description('Menunggu verifikasi HRD / Direksi')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->url('/portal/leave-requests');
        } elseif ($recentLeave) {
            $statusLabel = match ($recentLeave->status) {
                'Approved_Direksi' => 'Disetujui Direksi',
                'Approved_HR', 'Approved_HRD' => 'Disetujui HRD',
                'Approved_Komisaris' => 'Disetujui Komisaris',
                'Rejected' => 'Ditolak',
                default => $recentLeave->status,
            };

            $stats[] = Stat::make('Cuti Terakhir', ($recentLeave->leaveType?->name ?? 'Cuti') . " ({$recentLeave->total_days} Hari)")
                ->description("Status: {$statusLabel} • {$recentLeave->start_date->format('d/m/Y')}")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color($recentLeave->status === 'Rejected' ? 'danger' : 'success')
                ->url('/portal/leave-requests');
        } else {
            $stats[] = Stat::make('Pengajuan Berjalan', '0 Pengajuan')
                ->description('Tidak ada permohonan pending')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('gray')
                ->url('/portal/leave-requests');
        }

        return $stats;
    }
}
