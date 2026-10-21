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
        $blockBalance = $balances->firstWhere('leaveType.code', 'BLOCK_LEAVE');

        $pendingCount = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'Pending')
            ->count();

        $stats = [
            Stat::make('Sisa Cuti Tahunan', ($annualBalance->remaining_quota ?? 12) . ' Hari')
                ->description('Kuota Tahun ' . now()->year . ' • Klik untuk lihat riwayat')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success')
                ->url('/portal/leave-requests'),

            Stat::make('Status Block Leave OJK', ($blockBalance ? $blockBalance->remaining_quota . ' Hari Tersedia' : '5 Hari Tersedia'))
                ->description('Mandatori Bank • Klik untuk ajukan cuti')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('primary')
                ->url('/portal/leave-requests/create'),

            Stat::make('Pengajuan Berjalan', $pendingCount . ' Pengajuan')
                ->description('Status approval • Klik untuk cek status')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color($pendingCount > 0 ? 'warning' : 'gray')
                ->url('/portal/leave-requests'),
        ];

        return $stats;
    }
}
