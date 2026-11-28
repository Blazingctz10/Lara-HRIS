<?php

namespace App\Filament\Portal\Resources\LeaveRequests\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['leaveType']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tgl Pengajuan')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('division_category_label')
                    ->label('Bagian / Divisi')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('leave_type_display')
                    ->label('Jenis / Keterangan Cuti')
                    ->wrap()
                    ->weight('semibold'),

                TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Selesai')
                    ->date('d M Y'),

                TextColumn::make('total_days')
                    ->label('Durasi')
                    ->formatStateUsing(fn ($state) => "{$state} Hari")
                    ->color('primary')
                    ->alignCenter()
                    ->weight('bold'),

                TextColumn::make('reason')
                    ->label('Alasan / Keperluan Cuti')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->reason)
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('status')
                    ->label('Status Approval')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Approved_Direksi', 'Approved_HRD', 'Approved_Komisaris' => 'success',
                        'Approved_Leader', 'Approved_HR' => 'primary',
                        'Pending', 'Pending_Komisaris' => 'warning',
                        'Rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Approved_Direksi' => 'ACC Direksi (Disetujui)',
                        'Approved_Leader' => 'TTD Leader -> Menunggu TTD Direksi',
                        'Approved_HRD' => 'Disetujui SDM',
                        'Approved_Komisaris' => 'Disetujui Dewan Komisaris',
                        'Pending_Komisaris' => 'Menunggu Dewan Komisaris',
                        'Approved_HR' => 'Menunggu TTD Direksi',
                        'Pending' => 'Menunggu TTD Leader Divisi',
                        'Rejected' => 'Ditolak',
                        default => $state,
                    }),

                TextColumn::make('approver.name')
                    ->label('Disetujui Oleh')
                    ->placeholder('-'),

                TextColumn::make('rejection_reason')
                    ->label('Catatan Penolakan')
                    ->placeholder('-')
                    ->color('danger')
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->recordAction('view_details')
            ->recordActions([
                Action::make('view_details')
                    ->label('Lihat Bukti')
                    ->icon('heroicon-o-paper-clip')
                    ->color('info')
                    ->button()
                    ->outlined()
                    ->modalWidth('5xl')
                    ->modalHeading(fn (\App\Models\LeaveRequest $record) => "Lampiran Berkas Cuti: {$record->leaveType?->name}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (\App\Models\LeaveRequest $record) => view('filament.modals.leave-request-detail', ['record' => $record])),

                Action::make('print_form')
                    ->label('Cetak Formulir')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->button()
                    ->outlined()
                    ->url(fn (\App\Models\LeaveRequest $record) => route('leave-requests.print', $record))
                    ->openUrlInNewTab(),

                EditAction::make()->visible(fn ($record) => $record->status === 'Pending'),
            ]);
    }
}
