<?php

namespace App\Filament\Portal\Widgets;

use App\Models\Attendance;
use Filament\Widgets\Widget;

class TodayAttendanceWidget extends Widget
{
    protected string $view = 'filament.portal.widgets.today-attendance-widget';

    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    public function getTodayAttendance(): ?Attendance
    {
        $employee = auth()->user()?->employee;
        if (! $employee) {
            return null;
        }

        return Attendance::where('employee_id', $employee->id)
            ->whereDate('date', now())
            ->first();
    }
}
