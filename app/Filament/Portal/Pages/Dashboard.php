<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Widgets\AnnouncementsWidget;
use App\Filament\Portal\Widgets\MyLeaveBalanceWidget;
use App\Filament\Portal\Widgets\MyRecentReimbursementsWidget;
use App\Filament\Portal\Widgets\MyReimbursementStatsWidget;
use App\Filament\Portal\Widgets\PortalShortcutWidget;
use App\Filament\Portal\Widgets\TodayAttendanceWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Portal Karyawan';

    protected static ?string $navigationLabel = 'Dashboard';

    public function getWidgets(): array
    {
        return [
            PortalShortcutWidget::class,
            TodayAttendanceWidget::class,
            MyLeaveBalanceWidget::class,
            MyReimbursementStatsWidget::class,
            MyRecentReimbursementsWidget::class,
            AnnouncementsWidget::class,
        ];
    }
}
