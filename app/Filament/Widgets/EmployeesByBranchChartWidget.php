<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\Employee;
use Filament\Widgets\ChartWidget;

class EmployeesByBranchChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = '🏢 Komposisi SDM per Jaringan Kantor Cabang';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];

    protected function getData(): array
    {
        $branches = Branch::where('is_active', true)->withCount('employees')->get();

        $labels = [];
        $counts = [];
        $colors = [
            '#0E6838', // BPR Green
            '#F37A20', // BPR Orange
            '#FDB813', // BPR Gold
            '#0284c7', // Sky Blue
            '#8b5cf6', // Violet
            '#ec4899', // Pink
        ];

        foreach ($branches as $branch) {
            $labels[] = $branch->name . ' (' . $branch->type . ')';
            $counts[] = $branch->employees_count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pegawai',
                    'data' => $counts,
                    'backgroundColor' => array_slice($colors, 0, count($counts)),
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
