<?php

namespace App\Filament\Portal\Widgets;

use App\Models\Reimbursement;
use Filament\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MyRecentReimbursementsWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $employee = auth()->user()?->employee;

        return $table
            ->query(
                $employee
                    ? Reimbursement::where('employee_id', $employee->id)->latest('date')
                    : Reimbursement::whereRaw('1 = 0')
            )
            ->heading('Daftar Pengajuan Reimbursement & Status Pencairan Dana')
            ->description('Pantau pengeluaran operasional yang Anda ajukan, termasuk yang belum ditarik / belum dicairkan oleh kasir.')
            ->headerActions([
                Action::make('create_claim')
                    ->label('Ajukan Klaim Baru')
                    ->icon('heroicon-m-plus')
                    ->color('primary')
                    ->url('/portal/reimbursements/create'),
            ])
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal Nota')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Keperluan / Judul Klaim')
                    ->weight('bold')
                    ->searchable()
                    ->description(fn (Reimbursement $record): string => $record->category),

                TextColumn::make('amount')
                    ->label('Nominal Klaim')
                    ->money('IDR')
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('status')
                    ->label('Status Pencairan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Approved_Pincab' => 'warning',
                        'Pending' => 'info',
                        'Paid' => 'success',
                        'Rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Approved_Pincab' => 'Disetujui Pincab (Siap Cair / Belum Ditarik)',
                        'Pending' => 'Menunggu Persetujuan',
                        'Paid' => 'Sudah Dicairkan / Lunas',
                        'Rejected' => 'Ditolak',
                        default => $state,
                    }),

                ImageColumn::make('receipt_photo')
                    ->label('Bukti Struk')
                    ->circular(),

                TextColumn::make('payment_date')
                    ->label('Tgl Dicairkan')
                    ->date('d M Y')
                    ->placeholder('-'),
            ])
            ->paginated([5, 10]);
    }
}
