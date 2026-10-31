<?php

namespace App\Filament\Resources\Reimbursements\Tables;

use App\Models\Reimbursement;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReimbursementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['employee.branch']))
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('employee.branch.name')
                    ->label('Unit Kantor')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Judul Klaim')
                    ->searchable(),

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
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                ImageColumn::make('receipt_photo')
                    ->label('Struk')
                    ->circular()
                    ->action('view_details'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Approved_Pincab' => 'info',
                        'Paid' => 'success',
                        'Rejected' => 'danger',
                        default => 'gray',
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
                    ->modalHeading(fn (Reimbursement $record) => "Bukti Struk: {$record->title} ({$record->employee->full_name})")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (Reimbursement $record) => view('filament.modals.reimbursement-detail', ['record' => $record])),

                Action::make('approve_pincab')
                    ->label('Acc Pincab')
                    ->icon('heroicon-m-check')
                    ->color('info')
                    ->button()
                    ->visible(fn (Reimbursement $record) => $record->status === 'Pending' && (auth()->user()->isBranchManager() || auth()->user()->isHrAdmin()))
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Persetujuan Klaim (Pincab)')
                    ->modalDescription(fn (Reimbursement $record) => "Apakah Anda yakin ingin menyetujui klaim reimbursement '{$record->title}' sebesar Rp " . number_format($record->amount, 0, ',', '.') . " untuk pegawai {$record->employee->full_name}?")
                    ->modalSubmitActionLabel('Ya, Setujui Klaim')
                    ->modalCancelActionLabel('Batal')
                    ->modalIcon('heroicon-o-check-circle')
                    ->action(function (Reimbursement $record) {
                        $record->update([
                            'status' => 'Approved_Pincab',
                            'approved_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Klaim Disetujui Pincab')
                            ->body("Klaim Rp " . number_format($record->amount, 0, ',', '.') . " {$record->employee->full_name} telah disetujui Pincab.")
                            ->success()
                            ->send();
                    }),

                Action::make('mark_paid')
                    ->label('Bayar / Lunas')
                    ->icon('heroicon-m-banknotes')
                    ->color('success')
                    ->button()
                    ->visible(fn (Reimbursement $record) => in_array($record->status, ['Pending', 'Approved_Pincab']) && auth()->user()->isHrAdmin())
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pelunasan / Pencairan Dana Klaim')
                    ->modalDescription(fn (Reimbursement $record) => "Apakah Anda yakin dana sebesar Rp " . number_format($record->amount, 0, ',', '.') . " untuk klaim '{$record->title}' telah dicairkan / ditransfer kepada {$record->employee->full_name}? Status klaim akan diubah menjadi 'Paid' (Lunas).")
                    ->modalSubmitActionLabel('Ya, Konfirmasi Lunas')
                    ->modalCancelActionLabel('Batal')
                    ->modalIcon('heroicon-o-banknotes')
                    ->action(function (Reimbursement $record) {
                        $record->update([
                            'status' => 'Paid',
                            'payment_date' => now(),
                            'approved_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Klaim Telah Dibayarkan')
                            ->body("Klaim {$record->employee->full_name} telah ditandai lunas/transfer.")
                            ->success()
                            ->send();
                    }),

                EditAction::make()
                    ->button()
                    ->outlined()
                    ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
            ]);
    }
}
