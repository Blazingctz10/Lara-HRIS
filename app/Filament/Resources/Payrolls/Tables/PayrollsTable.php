<?php

namespace App\Filament\Resources\Payrolls\Tables;

use App\Models\Payroll;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
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
            ->modifyQueryUsing(fn ($query) => $query->with(['employee.branch', 'employee.position']))
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Payroll $record): string => ($record->employee?->nip ?? '-') . ' • ' . ($record->employee?->position?->name ?? '-')),

                TextColumn::make('period_label')
                    ->label('Periode Gaji')
                    ->badge()
                    ->color('primary')
                    ->sortable(['period_year', 'period_month']),

                TextColumn::make('employee.branch.name')
                    ->label('Unit Kantor')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('basic_salary')
                    ->label('Gaji Pokok')
                    ->formatStateUsing(fn ($state) => $state !== null ? 'Rp ' . number_format((float) $state, 0, ',', '.') : '-')
                    ->sortable(),

                TextColumn::make('total_earnings')
                    ->label('Total Bruto')
                    ->formatStateUsing(fn ($state) => $state !== null ? 'Rp ' . number_format((float) $state, 0, ',', '.') : '-')
                    ->color('info')
                    ->sortable(),

                TextColumn::make('deductions')
                    ->label('Potongan')
                    ->formatStateUsing(fn ($state) => $state !== null ? 'Rp ' . number_format((float) $state, 0, ',', '.') : '-')
                    ->color('danger')
                    ->sortable(),

                TextColumn::make('net_salary')
                    ->label('Gaji Bersih (THP)')
                    ->formatStateUsing(fn ($state) => $state !== null ? 'Rp ' . number_format((float) $state, 0, ',', '.') : '-')
                    ->weight('bold')
                    ->color('success')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Paid' => 'success',
                        'Approved' => 'primary',
                        'Draft' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('period_month')
                    ->label('Filter Bulan')
                    ->options([
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                    ]),

                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'Draft' => 'Draft',
                        'Approved' => 'Approved',
                        'Paid' => 'Paid (Rilis)',
                    ]),
            ])
            ->actions([
                Action::make('view_slip')
                    ->label('Slip Gaji')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->button()
                    ->outlined()
                    ->modalWidth('4xl')
                    ->modalHeading(fn (Payroll $record) => "Slip Gaji Resmi: {$record->employee?->full_name} ({$record->period_label})")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (Payroll $record) => view('filament.modals.payroll-slip-detail', ['record' => $record])),

                Action::make('mark_as_paid')
                    ->label('Rilis / Bayar')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Payroll $record): bool => $record->status !== 'Paid' && in_array(auth()->user()?->role, ['super_admin', 'hr_admin']))
                    ->requiresConfirmation()
                    ->modalHeading('Rilis Slip Gaji ke Portal Pegawai')
                    ->modalDescription('Slip gaji ini akan berstatus Paid dan otomatis dapat diakses oleh pegawai di Portal ESS pribadinya.')
                    ->action(function (Payroll $record) {
                        $record->update([
                            'status' => 'Paid',
                            'payment_date' => now(),
                        ]);

                        Notification::make()
                            ->title('Slip Gaji Berhasil Dirilis')
                            ->body("Slip gaji {$record->employee?->full_name} periode {$record->period_label} kini aktif dan dapat dilihat oleh yang bersangkutan.")
                            ->success()
                            ->send();
                    }),

                EditAction::make()
                    ->visible(fn (): bool => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
                DeleteAction::make()
                    ->visible(fn (): bool => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
                ]),
            ]);
    }
}
