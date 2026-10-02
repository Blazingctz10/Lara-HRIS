<?php

namespace App\Filament\Portal\Widgets;

use App\Models\Attendance;
use Filament\Widgets\Widget;

class TodayAttendanceWidget extends Widget
{
    protected string $view = 'filament.portal.widgets.today-attendance-widget';

    protected static ?int $sort = 1;

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

    public function getTodaySchedule(): array
    {
        $dayOfWeek = now()->dayOfWeek; // 0 = Sun, 1 = Mon, ..., 5 = Fri, 6 = Sat

        return match ($dayOfWeek) {
            0 => [
                'day_name' => 'Minggu',
                'hours' => 'Libur Operasional',
                'is_work_day' => false,
                'badge' => 'Libur Operasional',
            ],
            5 => [
                'day_name' => 'Jumat',
                'hours' => '08:00 - 16:30 WIB',
                'is_work_day' => true,
                'badge' => '08:00 - 16:30 WIB (Jumat)',
            ],
            6 => [
                'day_name' => 'Sabtu',
                'hours' => '08:00 - 13:00 WIB',
                'is_work_day' => true,
                'badge' => '08:00 - 13:00 WIB (Sabtu)',
            ],
            default => [
                'day_name' => now()->translatedFormat('l'),
                'hours' => '08:00 - 16:00 WIB',
                'is_work_day' => true,
                'badge' => '08:00 - 16:00 WIB (Senin - Kamis)',
            ],
        };
    }
}
