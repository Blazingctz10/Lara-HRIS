<?php

namespace App\Filament\Portal\Resources\Overtimes\Tables;

use App\Models\Overtime;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OvertimesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal Lembur')
                    ->date('d M Y')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('start_time')
                    ->label('Mulai')
                    ->time('H:i'),

                TextColumn::make('end_time')
                    ->label('Selesai')
                    ->time('H:i'),

                TextColumn::make('duration_hours')
                    ->label('Durasi')
                    ->suffix(' Jam')
                    ->badge()
                    ->color('warning')
                    ->alignCenter(),

                TextColumn::make('reason')
                    ->label('Alasan / Pekerjaan')
                    ->limit(35)
                    ->tooltip(fn (Overtime $record) => $record->reason),

                TextColumn::make('estimated_compensation')
                    ->label('Estimasi Uang Lembur')
                    ->formatStateUsing(fn (Overtime $record): string => 'Rp ' . number_format($record->estimated_compensation, 0, ',', '.'))
                    ->badge()
                    ->color('success')
                    ->weight('bold')
                    ->description(fn (Overtime $record): string => $record->duration_hours . ' Jam @ Rp ' . number_format($record->hourly_rate * 1.5, 0, ',', '.') . '/jam'),

                TextColumn::make('status')
                    ->label('Status Approval')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Approved_HR', 'Approved', 'Approved_Pincab' => 'success',
                        'Rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Pending' => 'Menunggu Persetujuan HR',
                        'Approved_HR', 'Approved', 'Approved_Pincab' => 'Disetujui HR',
                        'Rejected' => 'Ditolak',
                        default => $state,
                    }),
            ])
            ->recordActions([
                EditAction::make()->visible(fn (Overtime $record) => $record->status === 'Pending'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
