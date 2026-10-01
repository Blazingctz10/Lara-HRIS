<?php

namespace App\Filament\Portal\Widgets;

use App\Models\Reimbursement;
use Filament\Widgets\Widget;

class PortalShortcutWidget extends Widget
{
    protected string $view = 'filament.portal.widgets.portal-shortcut-widget';

    protected static bool $isLazy = false;

    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    public function getData(): array
    {
        $employee = auth()->user()?->employee;
        $unpaidReimbursements = $employee
            ? Reimbursement::where('employee_id', $employee->id)->where('status', 'Approved_Pincab')->get()
            : collect();

        $unpaidCount = $unpaidReimbursements->count();
        $unpaidSum = (float) $unpaidReimbursements->sum('amount');

        return [
            'unpaidCount' => $unpaidCount,
            'unpaidSum' => $unpaidSum,
        ];
    }
}
