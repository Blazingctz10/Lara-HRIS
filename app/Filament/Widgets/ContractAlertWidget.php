<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class ContractAlertWidget extends Widget
{
    protected static ?int $sort = 1;

    protected string $view = 'filament.widgets.contract-alert-widget';

    public function getExpiringContracts()
    {
        $today = Carbon::today();
        $targetDate = Carbon::today()->addDays(60);

        return Employee::accessibleBy()
            ->with(['branch', 'position', 'department'])
            ->where('status', 'Active')
            ->where(function ($query) use ($today, $targetDate) {
                $query->whereBetween('contract_end_date', [$today, $targetDate])
                    ->orWhereBetween('probation_end_date', [$today, $targetDate]);
            })
            ->orderBy('contract_end_date')
            ->get();
    }
}
