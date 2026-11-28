<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\JobApplicant;
use App\Models\LeaveRequest;
use App\Models\Reimbursement;
use App\Models\User;
use App\Services\HrisDashboardCacheService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BankStatsOverview extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'hr_admin', 'branch_manager', 'direksi', 'compliance']);
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $isBranchManager = $user?->isBranchManager() ?? false;

        if (! $isBranchManager) {
            $metrics = app(HrisDashboardCacheService::class)->getMetrics();
            $totalEmployees = (int) ($metrics['total_employees'] ?? 0);
            $activeEmployees = (int) ($metrics['active_employees'] ?? 0);
            $attendanceToday = (int) ($metrics['attendance_today'] ?? 0);
            $totalBranches = (int) ($metrics['active_branches'] ?? 0);
            $unpaidClaimsCount = (int) ($metrics['unpaid_claims_count'] ?? 0);
            $unpaidClaimsSum = (float) ($metrics['unpaid_claims_sum'] ?? 0);
            $activeUsers = (int) ($metrics['active_users'] ?? 0);
        } else {
            $employeeQuery = Employee::accessibleBy();
            $totalEmployees = $employeeQuery->count();
            $activeEmployees = (clone $employeeQuery)->where('status', 'Active')->count();

            $attendanceToday = Attendance::accessibleBy()->whereDate('date', now())->count();
            $totalBranches = Branch::where('is_active', true)->count();

            $unpaidClaimsQuery = Reimbursement::accessibleBy()->whereIn('status', ['Pending', 'Approved_Pincab']);
            $unpaidClaimsCount = $unpaidClaimsQuery->count();
            $unpaidClaimsSum = (float) $unpaidClaimsQuery->sum('amount');
            $activeUsers = 0;
        }

        // 1. IT ADMIN / SUPER ADMIN
        if ($user && $user->role === 'super_admin') {

            return [
                Stat::make('Akun Sistem Aktif', "{$activeUsers} Akun")
                    ->description('Kredensial login aktif terdaftar')
                    ->descriptionIcon('heroicon-o-users')
                    ->color('primary'),

                Stat::make('Jaringan Kantor BPR', "{$totalBranches} Kantor")
                    ->description('Kantor Pusat, Cabang & Kas')
                    ->descriptionIcon('heroicon-o-building-office-2')
                    ->color('primary'),

                Stat::make('Pegawai Aktif', "{$activeEmployees} Pegawai")
                    ->description('Pegawai operasional aktif')
                    ->descriptionIcon('heroicon-o-user-group')
                    ->color('info'),

                Stat::make('Log Presensi Masuk Hari Ini', "{$attendanceToday} Data")
                    ->description('Transaksi kehadiran tersimpan')
                    ->descriptionIcon('heroicon-o-clock')
                    ->color('success'),

                Stat::make('Klaim Reimbursement Aktif', 'Rp ' . number_format($unpaidClaimsSum, 0, ',', '.'))
                    ->description($unpaidClaimsCount > 0 ? "{$unpaidClaimsCount} klaim pending / siap cair" : 'Tidak ada antrean klaim')
                    ->descriptionIcon('heroicon-o-receipt-percent')
                    ->color($unpaidClaimsCount > 0 ? 'warning' : 'gray'),
            ];
        }

        // 2. DIREKSI (Executive Overview & Final Approval)
        if ($user && $user->role === 'direksi') {
            $pendingDireksi = LeaveRequest::where('status', 'Approved_HR')->count();

            return [
                Stat::make('Total Pegawai BPR', "{$activeEmployees} Pegawai")
                    ->description('Pegawai operasional aktif')
                    ->descriptionIcon('heroicon-o-user-group')
                    ->color('primary'),

                Stat::make('Presensi Hari Ini', "{$attendanceToday} Hadir")
                    ->description('Kehadiran pegawai seluruh cabang')
                    ->descriptionIcon('heroicon-o-check-badge')
                    ->color('success'),

                Stat::make('Otorisasi Cuti Direksi', "{$pendingDireksi} Berkas")
                    ->description($pendingDireksi > 0 ? 'Menunggu persetujuan Direksi' : 'Semua otorisasi selesai')
                    ->descriptionIcon('heroicon-o-pencil-square')
                    ->color($pendingDireksi > 0 ? 'danger' : 'gray'),

                Stat::make('Beban Klaim Operasional', 'Rp ' . number_format($unpaidClaimsSum, 0, ',', '.'))
                    ->description("{$unpaidClaimsCount} klaim aktif dalam proses")
                    ->descriptionIcon('heroicon-o-banknotes')
                    ->color('warning'),

                Stat::make('Jaringan Kantor BPR', "{$totalBranches} Kantor")
                    ->description('Kantor Pusat & Kantor Cabang')
                    ->descriptionIcon('heroicon-o-building-office-2')
                    ->color('info'),
            ];
        }

        // 3. HR ADMIN
        if ($user && $user->role === 'hr_admin') {
            $pendingHrLeaves = LeaveRequest::where('status', 'Pending')->count();
            $newApplicants = JobApplicant::where('status', 'Applied')->count();

            return [
                Stat::make('Total Pegawai Aktif', "{$activeEmployees} Pegawai")
                    ->description('Status operasional aktif')
                    ->descriptionIcon('heroicon-o-user-group')
                    ->color('success'),

                Stat::make('Presensi Hari Ini', "{$attendanceToday} Pegawai")
                    ->description('Tercatat masuk sistem')
                    ->descriptionIcon('heroicon-o-clock')
                    ->color('info'),

                Stat::make('Verifikasi Cuti Pending', "{$pendingHrLeaves} Berkas")
                    ->description('Menunggu verifikasi HR')
                    ->descriptionIcon('heroicon-o-document-text')
                    ->color($pendingHrLeaves > 0 ? 'warning' : 'gray'),

                Stat::make('Pelamar Baru', "{$newApplicants} Berkas")
                    ->description('Kandidat rekrutmen masuk')
                    ->descriptionIcon('heroicon-o-briefcase')
                    ->color($newApplicants > 0 ? 'primary' : 'gray'),

                Stat::make('Klaim Reimbursement Aktif', 'Rp ' . number_format($unpaidClaimsSum, 0, ',', '.'))
                    ->description($unpaidClaimsCount > 0 ? "{$unpaidClaimsCount} klaim pending / siap cair" : 'Tidak ada antrean klaim')
                    ->descriptionIcon('heroicon-o-receipt-percent')
                    ->color($unpaidClaimsCount > 0 ? 'warning' : 'gray'),
            ];
        }

        // 4. PETUGAS KEPATUHAN / COMPLIANCE
        if ($user && $user->role === 'compliance') {
            $policyCount = \App\Models\CompliancePolicy::count();
            $ojkPolicyCount = \App\Models\CompliancePolicy::where('category', 'apu_ppt')->count();
            $sopPolicyCount = \App\Models\CompliancePolicy::where('category', 'sop_operation')->count();
            $activeAnnouncements = \App\Models\Announcement::where('is_active', true)->count();

            return [
                Stat::make('Katalog Kebijakan & SOP', "{$policyCount} Regulasi")
                    ->description('Pedoman aktif berlaku bank')
                    ->descriptionIcon('heroicon-o-shield-check')
                    ->color('success')
                    ->url('/admin/compliance-policies'),

                Stat::make('Regulasi APU & PPT (OJK)', "{$ojkPolicyCount} Dokumen")
                    ->description('Kepatuhan anti pencucian uang')
                    ->descriptionIcon('heroicon-o-scale')
                    ->color('danger')
                    ->url('/admin/compliance-policies'),

                Stat::make('SOP Operasional & Kas', "{$sopPolicyCount} Prosedur")
                    ->description('Standar operasional cabang & teller')
                    ->descriptionIcon('heroicon-o-banknotes')
                    ->color('primary')
                    ->url('/admin/compliance-policies'),

                Stat::make('Surat Edaran Aktif', "{$activeAnnouncements} Memo")
                    ->description('Edaran & pengumuman internal bank')
                    ->descriptionIcon('heroicon-o-megaphone')
                    ->color('warning')
                    ->url('/admin/announcements'),

                Stat::make('Jaringan Kantor BPR', "{$totalBranches} Kantor")
                    ->description('Kantor Pusat & Kantor Cabang')
                    ->descriptionIcon('heroicon-o-building-office-2')
                    ->color('gray'),
            ];
        }

        // 5. PEMIMPIN CABANG (Branch Manager)
        if ($user && $user->role === 'branch_manager') {
            $userBranchId = $user->branch_id ?? $user->employee?->branch_id;
            $branchEmployeesCount = Employee::where('branch_id', $userBranchId)->where('status', 'Active')->count();
            $branchAttendanceToday = Attendance::where('branch_id', $userBranchId)->whereDate('date', now())->count();
            $branchPendingLeaves = LeaveRequest::accessibleBy()->where('status', 'Pending')->count();
            $branchPendingClaims = Reimbursement::accessibleBy()->where('status', 'Pending')->count();

            return [
                Stat::make('Pegawai Cabang Aktif', "{$branchEmployeesCount} Pegawai")
                    ->description($user->branch?->name ?? 'Cabang Penempatan')
                    ->descriptionIcon('heroicon-o-user-group')
                    ->color('primary'),

                Stat::make('Presensi Cabang Hari Ini', "{$branchAttendanceToday} Hadir")
                    ->description('Kehadiran staf kantor cabang')
                    ->descriptionIcon('heroicon-o-clock')
                    ->color('success'),

                Stat::make('Persetujuan Cuti Cabang', "{$branchPendingLeaves} Berkas")
                    ->description($branchPendingLeaves > 0 ? 'Menunggu review Pincab' : 'Tidak ada cuti pending')
                    ->descriptionIcon('heroicon-o-document-text')
                    ->color($branchPendingLeaves > 0 ? 'warning' : 'gray')
                    ->url('/admin/leave-requests'),

                Stat::make('Klaim Reimbursement Cabang', "{$branchPendingClaims} Berkas")
                    ->description($branchPendingClaims > 0 ? 'Menunggu persetujuan Pincab' : 'Semua klaim diproses')
                    ->descriptionIcon('heroicon-o-receipt-percent')
                    ->color($branchPendingClaims > 0 ? 'warning' : 'gray')
                    ->url('/admin/reimbursements'),
            ];
        }

        // 6. GENERAL / DEFAULT OVERVIEW
        $pendingLeaves = LeaveRequest::whereIn('status', ['Pending', 'Approved_HR'])->count();

        return [
            Stat::make('Total Pegawai Aktif', "{$activeEmployees} Pegawai")
                ->description('Status operasional bank')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('success'),

            Stat::make('Presensi Hari Ini', "{$attendanceToday} Hadir")
                ->description('Kehadiran pegawai')
                ->descriptionIcon('heroicon-o-clock')
                ->color('info'),

            Stat::make('Pengajuan Cuti Berjalan', "{$pendingLeaves} Berkas")
                ->description('Dalam proses verifikasi/otorisasi')
                ->descriptionIcon('heroicon-o-document-text')
                ->color($pendingLeaves > 0 ? 'warning' : 'gray'),

            Stat::make('Klaim Reimbursement Aktif', 'Rp ' . number_format($unpaidClaimsSum, 0, ',', '.'))
                ->description($unpaidClaimsCount > 0 ? "{$unpaidClaimsCount} klaim diajukan" : 'Tidak ada klaim pending')
                ->descriptionIcon('heroicon-o-receipt-percent')
                ->color($unpaidClaimsCount > 0 ? 'warning' : 'gray'),

            Stat::make('Jaringan Kantor BPR', "{$totalBranches} Kantor")
                ->description('Kantor Pusat, Cabang & Kas')
                ->descriptionIcon('heroicon-o-building-office')
                ->color('gray'),
        ];
    }
}
