<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Widgets\AnnouncementsWidget;
use App\Filament\Portal\Widgets\MyLeaveHistoryWidget;
use App\Filament\Portal\Widgets\PortalShortcutWidget;
use App\Filament\Portal\Widgets\TodayAttendanceWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Portal Pegawai';

    protected static ?string $navigationLabel = 'Dashboard';

    public function getWidgets(): array
    {
        return [
            PortalShortcutWidget::class,
            TodayAttendanceWidget::class,
            AnnouncementsWidget::class,
            MyLeaveHistoryWidget::class,
        ];
    }
}
