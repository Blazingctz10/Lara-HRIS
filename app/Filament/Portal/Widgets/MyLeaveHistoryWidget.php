<?php

namespace App\Filament\Portal\Widgets;

use App\Models\LeaveRequest;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MyLeaveHistoryWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $employee = auth()->user()?->employee;

        return $table
            ->query(
                $employee
                    ? LeaveRequest::where('employee_id', $employee->id)->with(['leaveType', 'approver'])->latest('created_at')
                    : LeaveRequest::whereRaw('1 = 0')
            )
            ->heading('Riwayat Pengajuan & Pemotongan Saldo Cuti')
            ->description('Rincian rekam jejak cuti yang Anda ajukan, alasan pengambilan cuti, dan pengurangan kuota saldo hak cuti tahun berjalan.')
            ->headerActions([
                Action::make('create_leave')
                    ->label('Ajukan Cuti Baru')
                    ->icon('heroicon-m-plus')
                    ->color('primary')
                    ->url('/portal/leave-requests/create'),
            ])
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tgl Pengajuan')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('leaveType.name')
                    ->label('Jenis Cuti')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('start_date')
                    ->label('Mulai Cuti')
                    ->date('d M Y'),

                TextColumn::make('end_date')
                    ->label('Selesai Cuti')
                    ->date('d M Y'),

                TextColumn::make('total_days')
                    ->label('Hari Dipotong')
                    ->formatStateUsing(fn ($state) => "-{$state} Hari")
                    ->color('danger')
                    ->weight('bold')
                    ->alignCenter(),

                TextColumn::make('reason')
                    ->label('Alasan / Keperluan Cuti')
                    ->limit(40)
                    ->tooltip(fn (LeaveRequest $record) => $record->reason)
                    ->placeholder('-'),

                TextColumn::make('status')
                    ->label('Status Approval')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Approved_Direksi', 'Approved_HRD', 'Approved_Komisaris' => 'success',
                        'Approved_HR' => 'info',
                        'Pending', 'Pending_Komisaris' => 'warning',
                        'Rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Approved_Direksi' => 'Disetujui Direksi (Final)',
                        'Approved_HRD' => 'ACC HRD (Final Direksi ≤ 2 Hari)',
                        'Approved_Komisaris' => 'Disetujui Dewan Komisaris',
                        'Pending_Komisaris' => 'Menunggu Dewan Komisaris',
                        'Approved_HR' => 'Menunggu Otorisasi Direksi (Verifikasi HR OK)',
                        'Pending' => 'Menunggu Verifikasi HR',
                        'Rejected' => 'Ditolak',
                        default => $state,
                    }),
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
                    ->modalHeading(fn (LeaveRequest $record) => "Lampiran Berkas Cuti: {$record->leaveType?->name}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (LeaveRequest $record) => view('filament.modals.leave-request-detail', ['record' => $record])),

                Action::make('print_form')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->button()
                    ->outlined()
                    ->url(fn (LeaveRequest $record) => route('leave-requests.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->paginated([5, 10]);
    }
}
