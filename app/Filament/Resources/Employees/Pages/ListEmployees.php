<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Models\Employee;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
        ];
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make('Pegawai Aktif')
                ->icon('heroicon-o-check-circle')
                ->badge(Employee::where('status', 'Active')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'Active')),

            'resigned' => Tab::make('Alumni / Resign')
                ->icon('heroicon-o-user-minus')
                ->badge(Employee::where('status', 'Resigned')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'Resigned')),

            'all' => Tab::make('Semua Data Pegawai')
                ->icon('heroicon-o-users')
                ->badge(Employee::count())
                ->badgeColor('primary'),
        ];
    }
}
