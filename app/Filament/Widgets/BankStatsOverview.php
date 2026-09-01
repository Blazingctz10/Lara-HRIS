<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BankStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();

        $employeeQuery = Employee::accessibleBy();
        $totalEmployees = $employeeQuery->count();
        $activeEmployees = (clone $employeeQuery)->where('status', 'Active')->count();

        $attendanceToday = Attendance::accessibleBy()->whereDate('date', now())->count();
        $pendingLeaves = LeaveRequest::accessibleBy()->where('status', 'Pending')->count();
        $totalBranches = Branch::where('is_active', true)->count();

        return [
            Stat::make('Total Pegawai Aktif', "{$activeEmployees} / {$totalEmployees}")
                ->description('Status operasional aktif')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Presensi Hari Ini', $attendanceToday . ' Pegawai')
                ->description('Tercatat masuk sistem')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),

            Stat::make('Pengajuan Cuti Pending', $pendingLeaves . ' Berkas')
                ->description('Menunggu persetujuan')
                ->descriptionIcon('heroicon-m-document-text')
                ->color($pendingLeaves > 0 ? 'warning' : 'gray'),

            Stat::make('Jaringan Kantor', $totalBranches . ' Cabang')
                ->description('HO, KC, KCP & KK aktif')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary'),
        ];
    }
}
