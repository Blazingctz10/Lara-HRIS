<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use Filament\Widgets\ChartWidget;

class HrDemographicsChartWidget extends ChartWidget
{
    protected ?string $heading = 'Distribusi Jenjang Pendidikan & Gender Pegawai';

    protected static ?int $sort = 4;

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'hr_admin', 'direksi']);
    }

    protected function getData(): array
    {
        $levels = ['S-1', 'D-3', 'SMA/SMK', 'S-2', 'Lainnya'];
        $maleCounts = [];
        $femaleCounts = [];

        foreach ($levels as $lvl) {
            if ($lvl === 'Lainnya') {
                $maleCounts[] = Employee::where('status', 'Active')
                    ->where('gender', 'Male')
                    ->whereNotIn('education_level', ['S-1', 'D-3', 'SMA/SMK', 'S-2'])
                    ->count();

                $femaleCounts[] = Employee::where('status', 'Active')
                    ->where('gender', 'Female')
                    ->whereNotIn('education_level', ['S-1', 'D-3', 'SMA/SMK', 'S-2'])
                    ->count();
            } else {
                $maleCounts[] = Employee::where('status', 'Active')
                    ->where('gender', 'Male')
                    ->where('education_level', 'LIKE', "%{$lvl}%")
                    ->count();

                $femaleCounts[] = Employee::where('status', 'Active')
                    ->where('gender', 'Female')
                    ->where('education_level', 'LIKE', "%{$lvl}%")
                    ->count();
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Laki-laki',
                    'data' => $maleCounts,
                    'backgroundColor' => '#0284c7', // Sky Blue
                ],
                [
                    'label' => 'Perempuan',
                    'data' => $femaleCounts,
                    'backgroundColor' => '#ec4899', // Pink
                ],
            ],
            'labels' => $levels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
