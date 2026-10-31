<?php

namespace App\Filament\Portal\Resources\Payrolls;

use App\Filament\Portal\Resources\Payrolls\Pages\ListPayrolls;
use App\Models\Payroll;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|UnitEnum|null $navigationGroup = 'Keuangan & Slip Saya';

    protected static ?string $navigationLabel = 'Slip Gaji Saya';

    protected static ?string $modelLabel = 'Slip Gaji';

    protected static ?string $pluralModelLabel = 'Riwayat Slip Gaji Saya';

    protected static ?int $navigationSort = 4;

    /**
     * KARYAWAN HANYA BISA LIHAT DATA MILIK SENDIRI YANG SUDAH RESMI DIRILIS/PAID
     */
    public static function canViewAny(): bool
    {
        return auth()->user()?->employee !== null;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $employee = auth()->user()?->employee;

        if (! $employee) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->where('employee_id', $employee->id)
            ->whereIn('status', ['Approved', 'Paid']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period_label')
                    ->label('Periode Pembayaran')
                    ->badge()
                    ->color('primary')
                    ->weight('bold'),

                TextColumn::make('payment_date')
                    ->label('Tanggal Transfer')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('basic_salary')
                    ->label('Gaji Pokok')
                    ->formatStateUsing(fn ($state) => $state !== null ? 'Rp ' . number_format((float) $state, 0, ',', '.') : '-'),

                TextColumn::make('total_earnings')
                    ->label('Total Bruto')
                    ->formatStateUsing(fn ($state) => $state !== null ? 'Rp ' . number_format((float) $state, 0, ',', '.') : '-')
                    ->color('info'),

                TextColumn::make('deductions')
                    ->label('Total Potongan')
                    ->formatStateUsing(fn ($state) => $state !== null ? 'Rp ' . number_format((float) $state, 0, ',', '.') : '-')
                    ->color('danger'),

                TextColumn::make('net_salary')
                    ->label('Take Home Pay (THP)')
                    ->formatStateUsing(fn ($state) => $state !== null ? 'Rp ' . number_format((float) $state, 0, ',', '.') : '-')
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Paid' => 'success',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn ($state) => $state === 'Paid' ? 'Diterima' : 'Diproses'),
            ])
            ->defaultSort('period_year', 'desc')
            ->actions([
                Action::make('view_slip')
                    ->label('Lihat Rincian Slip')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->button()
                    ->modalWidth('4xl')
                    ->modalHeading(fn (Payroll $record) => "Rincian Slip Gaji: {$record->period_label}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (Payroll $record) => view('filament.modals.payroll-slip-detail', ['record' => $record])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrolls::route('/'),
        ];
    }
}
