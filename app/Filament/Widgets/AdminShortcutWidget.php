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

    public function getData(): array
    {
        $role = auth()->user()?->role ?? 'employee';

        $unpaidClaimsCount = Reimbursement::accessibleBy()
            ->whereIn('status', ['Pending', 'Approved_Pincab'])
            ->count();

        $pendingLeaveCount = LeaveRequest::accessibleBy()
            ->whereIn('status', $role === 'direksi' ? ['Approved_HR', 'Pending'] : ['Pending'])
            ->count();

        $newApplicantsCount = JobApplicant::where('status', 'Baru')
            ->count();

        $totalUsersCount = \App\Models\User::count();
        $fingerprintOnlineCount = \App\Models\FingerprintDevice::where('status', 'Online')->count();
        $overtimePendingCount = \App\Models\Overtime::accessibleBy()->where('status', 'Pending')->count();

        return [
            'role' => $role,
            'unpaidClaimsCount' => $unpaidClaimsCount,
            'pendingLeaveCount' => $pendingLeaveCount,
            'newApplicantsCount' => $newApplicantsCount,
            'totalUsersCount' => $totalUsersCount,
            'fingerprintOnlineCount' => $fingerprintOnlineCount,
            'overtimePendingCount' => $overtimePendingCount,
        ];
    }
}
