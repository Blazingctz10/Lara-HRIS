<?php

namespace App\Filament\Portal\Widgets;

use App\Models\Reimbursement;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MyReimbursementStatsWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        $employee = auth()->user()?->employee;

        if (! $employee) {
            return [];
        }

        // 1. Klaim yang sudah disetujui tapi BELUM DITARIK / BELUM DITRANSFER (Approved_Pincab)
        $approvedQuery = Reimbursement::where('employee_id', $employee->id)
            ->where('status', 'Approved_Pincab');
        $approvedCount = $approvedQuery->count();
        $approvedSum = (float) $approvedQuery->sum('amount');

        // 2. Klaim yang masih dalam proses verifikasi (Pending)
        $pendingQuery = Reimbursement::where('employee_id', $employee->id)
            ->where('status', 'Pending');
        $pendingCount = $pendingQuery->count();
        $pendingSum = (float) $pendingQuery->sum('amount');

        // 3. Klaim yang SUDAH DITARIK / SUDAH DICAIRKAN bulan ini (Paid)
        $paidQuery = Reimbursement::where('employee_id', $employee->id)
            ->where('status', 'Paid')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year);
        $paidCount = $paidQuery->count();
        $paidSum = (float) $paidQuery->sum('amount');

        return [
            Stat::make(
                'Belum Ditarik / Siap Cair',
                'Rp ' . number_format($approvedSum, 0, ',', '.')
            )
                ->description($approvedCount > 0 ? "{$approvedCount} klaim disetujui Pincab (siap cair kasir)" : "Tidak ada dana siap tarik")
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($approvedCount > 0 ? 'warning' : 'gray')
                ->url('/portal/reimbursements'),

            Stat::make(
                'Klaim Menunggu Review',
                'Rp ' . number_format($pendingSum, 0, ',', '.')
            )
                ->description($pendingCount > 0 ? "{$pendingCount} berkas menunggu persetujuan Pincab" : "Semua pengajuan telah diproses")
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingCount > 0 ? 'info' : 'gray')
                ->url('/portal/reimbursements'),

            Stat::make(
                'Sudah Cair Bulan Ini',
                'Rp ' . number_format($paidSum, 0, ',', '.')
            )
                ->description("{$paidCount} klaim telah lunas ditransfer")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->url('/portal/reimbursements'),
        ];
    }
}
