<?php

namespace App\Filament\Resources\Payrolls\Tables;

use App\Models\Payroll;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PayrollsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('period_year', 'desc')
            ->columns([
                TextColumn::make('period_label')
                    ->label('Periode Gaji')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('employee.nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('employee.full_name')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('employee.branch.name')
                    ->label('Cabang')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('basic_salary')
                    ->label('Gaji Pokok')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('allowances')
                    ->label('Tunjangan')
                    ->money('IDR')
                    ->color('success'),

                TextColumn::make('deductions')
                    ->label('Potongan')
                    ->money('IDR')
                    ->color('danger'),

                TextColumn::make('net_salary')
                    ->label('Gaji Bersih')
                    ->money('IDR')
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Paid' => 'success',
                        'Approved' => 'info',
                        'Draft' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('payment_date')
                    ->label('Tgl Bayar')
                    ->date('d M Y')
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('period_month')
                    ->label('Bulan')
                    ->options([
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                    ]),
                SelectFilter::make('period_year')
                    ->label('Tahun')
                    ->options([
                        2025 => '2025',
                        2026 => '2026',
                        2027 => '2027',
                    ]),
                SelectFilter::make('status')
                    ->label('Status Slip')
                    ->options([
                        'Draft' => 'Draft',
                        'Approved' => 'Approved',
                        'Paid' => 'Paid',
                    ]),
            ])
            ->recordActions([
                Action::make('mark_as_paid')
                    ->label('Tandai Dibayar')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->visible(fn (Payroll $record) => $record->status !== 'Paid')
                    ->requiresConfirmation()
                    ->action(function (Payroll $record) {
                        $record->update([
                            'status' => 'Paid',
                            'payment_date' => now(),
                        ]);

                        Notification::make()
                            ->title('Status slip gaji diubah menjadi Paid')
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
