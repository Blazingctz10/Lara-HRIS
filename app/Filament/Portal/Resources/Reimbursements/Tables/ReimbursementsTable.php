<?php

namespace App\Filament\Portal\Resources\Reimbursements\Tables;

use App\Models\Reimbursement;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReimbursementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Klaim')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('amount')
                    ->label('Nominal')
                    ->formatStateUsing(fn ($state) => $state !== null ? 'Rp ' . number_format((float) $state, 0, ',', '.') : '-')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('date')
                    ->label('Tanggal Klaim')
                    ->date('d M Y')
                    ->sortable(),

                ImageColumn::make('receipt_photo')
                    ->label('Struk')
                    ->circular(),

                TextColumn::make('status')
                    ->label('Status Approval')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Approved_Pincab' => 'info',
                        'Paid' => 'success',
                        'Rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Pending' => 'Menunggu Review Pincab',
                        'Approved_Pincab' => 'Disetujui Pincab (Proses Kasir)',
                        'Paid' => 'Telah Ditransfer / Cair',
                        'Rejected' => 'Ditolak',
                        default => $state,
                    }),
            ])
            ->recordAction('view_details')
            ->recordActions([
                Action::make('view_details')
                    ->label('Lihat Struk')
                    ->icon('heroicon-o-receipt-percent')
                    ->color('info')
                    ->button()
                    ->outlined()
                    ->modalWidth('5xl')
                    ->modalHeading(fn (Reimbursement $record) => "Bukti Struk: {$record->title}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (Reimbursement $record) => view('filament.modals.reimbursement-detail', ['record' => $record])),

                EditAction::make()->visible(fn (Reimbursement $record) => $record->status === 'Pending'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
