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
        $unpaidClaimsCount = Reimbursement::accessibleBy()
            ->whereIn('status', ['Pending', 'Approved_Pincab'])
            ->count();

        $pendingLeaveCount = LeaveRequest::accessibleBy()
            ->where('status', 'Pending')
            ->count();

        $newApplicantsCount = JobApplicant::where('status', 'Baru')
            ->count();

        return [
            'unpaidClaimsCount' => $unpaidClaimsCount,
            'pendingLeaveCount' => $pendingLeaveCount,
            'newApplicantsCount' => $newApplicantsCount,
        ];
    }
}
