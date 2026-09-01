<?php

namespace App\Filament\Portal\Resources\Payrolls\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
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

                TextColumn::make('basic_salary')
                    ->label('Gaji Pokok')
                    ->money('IDR'),

                TextColumn::make('allowances')
                    ->label('Total Tunjangan')
                    ->money('IDR')
                    ->color('success'),

                TextColumn::make('deductions')
                    ->label('Total Potongan')
                    ->money('IDR')
                    ->color('danger'),

                TextColumn::make('net_salary')
                    ->label('Gaji Bersih (Take Home Pay)')
                    ->money('IDR')
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Paid' => 'success',
                        'Approved' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('payment_date')
                    ->label('Tgl Ditransfer')
                    ->date('d M Y')
                    ->placeholder('-'),
            ])
            ->recordActions([
                //
            ]);
    }
}
