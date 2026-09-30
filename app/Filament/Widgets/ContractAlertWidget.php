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
                // PKWT Contract expiring within 60 days
                $query->where(function ($q) use ($today, $targetDate) {
                    $q->whereIn('employment_status', ['Contract', 'Internship'])
                      ->where(function ($dateQ) use ($today, $targetDate) {
                          $dateQ->whereBetween('contract_end_date', [$today, $targetDate])
                                ->orWhere(function ($sub) use ($today, $targetDate) {
                                    $sub->whereNull('contract_end_date')
                                        ->whereBetween('end_date', [$today, $targetDate]);
                                });
                      });
                })
                // OR Probation expiring within 60 days (ONLY if currently on Probation)
                ->orWhere(function ($q) use ($today, $targetDate) {
                    $q->where('employment_status', 'Probation')
                      ->where(function ($dateQ) use ($today, $targetDate) {
                          $dateQ->whereBetween('probation_end_date', [$today, $targetDate])
                                ->orWhere(function ($sub) use ($today, $targetDate) {
                                    $sub->whereNull('probation_end_date')
                                        ->whereBetween('end_date', [$today, $targetDate]);
                                });
                      });
                });
            })
            ->get();
    }
}
