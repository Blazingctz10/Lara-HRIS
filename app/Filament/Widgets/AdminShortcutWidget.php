<?php

namespace App\Filament\Widgets;

use App\Models\JobApplicant;
use App\Models\LeaveRequest;
use App\Models\Reimbursement;
use Filament\Widgets\Widget;

class AdminShortcutWidget extends Widget
{
    protected string $view = 'filament.widgets.admin-shortcut-widget';

    protected static bool $isLazy = false;

    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'hr_admin', 'branch_manager', 'direksi', 'compliance']);
    }

    public function getData(): array
    {
        $user = auth()->user();
        $role = $user?->role ?? 'employee';

        if (! $user?->isBranchManager()) {
            $metrics = app(\App\Services\HrisDashboardCacheService::class)->getMetrics();

            return [
                'role' => $role,
                'unpaidClaimsCount' => (int) ($metrics['unpaid_claims_count'] ?? 0),
                'pendingLeaveCount' => match ($role) {
                    'direksi' => (int) ($metrics['pending_direksi_leaves'] ?? 0),
                    'hr_admin' => (int) ($metrics['pending_hr_leaves'] ?? 0),
                    default => (int) ($metrics['pending_hr_leaves'] ?? 0) + (int) ($metrics['pending_direksi_leaves'] ?? 0),
                },
                'newApplicantsCount' => (int) ($metrics['new_applicants'] ?? 0),
                'totalUsersCount' => (int) ($metrics['active_users'] ?? 0),
                'dispensationCount' => (int) ($metrics['dispensation_count'] ?? 0),
                'overtimePendingCount' => (int) ($metrics['pending_overtime'] ?? 0),
                'draftPayrollCount' => (int) ($metrics['draft_payroll'] ?? 0),
            ];
        }

        $unpaidClaimsCount = Reimbursement::accessibleBy()
            ->whereIn('status', ['Pending', 'Approved_Pincab'])
            ->count();

        $pendingLeaveCount = match ($role) {
            'direksi' => LeaveRequest::accessibleBy()->where('status', 'Approved_HR')->count(),
            'hr_admin' => LeaveRequest::accessibleBy()->where('status', 'Pending')->count(),
            'branch_manager' => LeaveRequest::accessibleBy()->where('status', 'Pending')->count(),
            default => LeaveRequest::accessibleBy()->whereIn('status', ['Pending', 'Approved_HR'])->count(),
        };

        $newApplicantsCount = JobApplicant::where('status', 'Baru')
            ->count();

        $totalUsersCount = \App\Models\User::where('is_active', true)
            ->where(fn ($q) => $q->whereDoesntHave('employee')->orWhereHas('employee', fn ($eq) => $eq->whereNotIn('status', ['Resigned', 'Terminated'])))
            ->count();
        $dispensationCount = \App\Models\AttendanceDispensation::count();
        $overtimePendingCount = \App\Models\Overtime::accessibleBy()->where('status', 'Pending')->count();
        $draftPayrollCount = \App\Models\Payroll::accessibleBy()->where('status', 'Draft')->count();

        return [
            'role' => $role,
            'unpaidClaimsCount' => $unpaidClaimsCount,
            'pendingLeaveCount' => $pendingLeaveCount,
            'newApplicantsCount' => $newApplicantsCount,
            'totalUsersCount' => $totalUsersCount,
            'dispensationCount' => $dispensationCount,
            'overtimePendingCount' => $overtimePendingCount,
            'draftPayrollCount' => $draftPayrollCount,
        ];
    }
}
