<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class WeeklyAttendanceChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = '📈 Tren Presensi Kerja Mingguan (Hadir & Tepat Waktu)';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];

    protected function getData(): array
    {
        $days = [];
        $presentCounts = [];
        $onTimeCounts = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $days[] = $date->translatedFormat('D, d M');

            $dayAttendance = Attendance::accessibleBy()
                ->whereDate('date', $date)
                ->get();

            $presentCounts[] = $dayAttendance->count();
            $onTimeCounts[] = $dayAttendance->where('clock_in', '<=', '08:00:00')->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pegawai Hadir',
                    'data' => $presentCounts,
                    'borderColor' => '#0E6838', // BPR Green
                    'backgroundColor' => 'rgba(14, 104, 56, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Tepat Waktu (< 08:00)',
                    'data' => $onTimeCounts,
                    'borderColor' => '#F37A20', // BPR Orange
                    'backgroundColor' => 'rgba(243, 122, 32, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $days,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
