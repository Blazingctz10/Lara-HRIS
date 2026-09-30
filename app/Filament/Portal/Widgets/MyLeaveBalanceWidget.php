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
                ->description('Kuota Tahun ' . now()->year)
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),

            Stat::make('Status Block Leave OJK', ($blockBalance ? $blockBalance->remaining_quota . ' Hari Tersedia' : '5 Hari Tersedia'))
                ->description('Mandatori Bank (Min. 5 Hari Beruntun)')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('primary'),

            Stat::make('Pengajuan Berjalan', $pendingCount . ' Pengajuan')
                ->description('Status menunggu approval')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color($pendingCount > 0 ? 'warning' : 'gray'),
        ];

        return $stats;
    }
}
