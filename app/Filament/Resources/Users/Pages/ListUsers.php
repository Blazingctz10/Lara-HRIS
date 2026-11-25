<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function getTabs(): array
    {
        $resignedCount = User::whereHas('employee', fn (Builder $q) => $q->whereIn('status', ['Resigned', 'Terminated']))->count();
        $totalCount = User::count();
        $activeCount = max(0, $totalCount - $resignedCount);

        return [
            'active' => Tab::make('Akun Aktif')
                ->icon('heroicon-o-check-circle')
                ->badge($activeCount)
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDoesntHave('employee', fn (Builder $q) => $q->whereIn('status', ['Resigned', 'Terminated']))),

            'resigned' => Tab::make('Alumni / Resign (Diblokir)')
                ->icon('heroicon-o-no-symbol')
                ->badge($resignedCount)
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('employee', fn (Builder $q) => $q->whereIn('status', ['Resigned', 'Terminated']))),

            'all' => Tab::make('Semua Akun')
                ->icon('heroicon-o-users')
                ->badge($totalCount)
                ->badgeColor('primary'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Akun Baru')
                ->icon('heroicon-o-user-plus'),
        ];
    }
}
