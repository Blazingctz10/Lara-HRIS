<?php

namespace App\Filament\Resources\Overtimes\Tables;

use App\Models\Overtime;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OvertimesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('employee.branch.name')
                    ->label('Kantor Cabang')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

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
                    ->label('Alasan / Kegiatan')
                    ->limit(35)
                    ->tooltip(fn (Overtime $record) => $record->reason),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Approved_Pincab' => 'info',
                        'Approved_HR' => 'success',
                        'Rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->recordActions([
                Action::make('approve_pincab')
                    ->label('Acc Pincab')
                    ->icon('heroicon-m-check')
                    ->color('info')
                    ->visible(fn (Overtime $record) => $record->status === 'Pending' && (auth()->user()->isBranchManager() || auth()->user()->isHrAdmin()))
                    ->action(function (Overtime $record) {
                        $record->update([
                            'status' => 'Approved_Pincab',
                            'approved_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Lembur Disetujui Pincab')
                            ->body("Pengajuan lembur {$record->employee->full_name} berhasil disetujui.")
                            ->success()
                            ->send();
                    }),

                Action::make('approve_hr')
                    ->label('Acc HR HO')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->visible(fn (Overtime $record) => in_array($record->status, ['Pending', 'Approved_Pincab']) && auth()->user()->isHrAdmin())
                    ->action(function (Overtime $record) {
                        $record->update([
                            'status' => 'Approved_HR',
                            'approved_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Lembur Disetujui HR HO')
                            ->body("Pengajuan lembur {$record->employee->full_name} telah diverifikasi dan disetujui HR HO.")
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
