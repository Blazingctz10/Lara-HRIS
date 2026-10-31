<?php

namespace App\Filament\Widgets;

use App\Models\JobApplicant;
use Filament\Widgets\ChartWidget;

class HrRecruitmentPipelineChartWidget extends ChartWidget
{
    protected ?string $heading = 'Funnel Rekrutmen & Status Pelamar Kerja';

    protected static ?int $sort = 5;

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'hr_admin', 'direksi']);
    }

    protected function getData(): array
    {
        $statuses = [
            'Baru' => 'Berkas Baru',
            'Screening' => 'Tahap Screening',
            'Interview' => 'Wawancara HR/User',
            'Diterima' => 'Diterima Pegawai',
            'Ditolak' => 'Tidak Lolos',
        ];

        $counts = [];
        $labels = [];
        $colors = ['#38bdf8', '#f59e0b', '#6366f1', '#10b981', '#ef4444'];

        foreach ($statuses as $key => $label) {
            $labels[] = $label;
            $counts[] = JobApplicant::where('status', $key)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pelamar',
                    'data' => $counts,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
