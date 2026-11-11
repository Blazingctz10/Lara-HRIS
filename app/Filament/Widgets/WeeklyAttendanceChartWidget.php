<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class WeeklyAttendanceChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Rekapitulasi Presensi Bulanan';

    protected ?string $maxHeight = '280px';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];

    public function getDescription(): ?string
    {
        return 'Rekap total kehadiran, cuti resmi, dan keterlambatan per periode penarikan data';
    }

    public static function canView(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'hr_admin', 'branch_manager', 'direksi']);
    }

    protected function getFilters(): ?array
    {
        return [
            '6' => '6 Bulan Terakhir',
            '12' => '12 Bulan Terakhir',
        ];
    }

    protected function getData(): array
    {
        $months = [];
        $presentCounts = [];
        $excusedCounts = [];
        $lateCounts = [];

        $limitMonths = (int) ($this->filter ?? 6);

        for ($i = $limitMonths - 1; $i >= 0; $i--) {
            $monthDate = Carbon::today()->startOfMonth()->subMonths($i);
            $monthEnd = $monthDate->copy()->endOfMonth();
            $months[] = $monthDate->translatedFormat('M Y');

            $records = Attendance::accessibleBy()
                ->whereDate('date', '>=', $monthDate->toDateString())
                ->whereDate('date', '<=', $monthEnd->toDateString())
                ->get();

            $presentCounts[] = $records->where('status', 'Present')->count();
            $excusedCounts[] = $records->where('status', 'Excused')->count();
            $lateCounts[] = $records->where('status', 'Late')->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Hadir Tepat Waktu',
                    'data' => $presentCounts,
                    'backgroundColor' => '#0E6838', // BPR Forest Green
                    'borderRadius' => 6,
                ],
                [
                    'label' => 'Cuti Resmi & Izin',
                    'data' => $excusedCounts,
                    'backgroundColor' => '#FDB813', // BPR Golden Ribbon
                    'borderRadius' => 6,
                ],
                [
                    'label' => 'Terlambat (Denda)',
                    'data' => $lateCounts,
                    'backgroundColor' => '#EA580C', // BPR Warm Orange
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
