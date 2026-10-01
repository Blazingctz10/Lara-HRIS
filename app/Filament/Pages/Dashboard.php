<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdminShortcutWidget;
use App\Filament\Widgets\BankStatsOverview;
use App\Filament\Widgets\ContractAlertWidget;
use App\Filament\Widgets\EmployeesByBranchChartWidget;
use App\Filament\Widgets\RecentLeaveRequestsWidget;
use App\Filament\Widgets\WeeklyAttendanceChartWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    public function getWidgets(): array
    {
        return [
            AdminShortcutWidget::class,
            BankStatsOverview::class,
            RecentLeaveRequestsWidget::class,
            EmployeesByBranchChartWidget::class,
            WeeklyAttendanceChartWidget::class,
            ContractAlertWidget::class,
        ];
    }
}
