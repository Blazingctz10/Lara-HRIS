<?php

namespace App\Filament\Resources\LeaveRequests\Pages;

use App\Filament\Resources\LeaveRequests\LeaveRequestResource;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Support\BprExcelExport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListLeaveRequests extends ListRecords
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_leave_balances')
                ->label('Unduh Rekap Saldo Cuti (Excel)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->modalHeading('Unduh Laporan Rekap Saldo & Kuota Cuti (.xlsx)')
                ->modalDescription('Format matriks ringkas 1 baris per pegawai (Sisa / Kuota Awal) sesuai hak cuti dan jenis kelamin pegawai.')
                ->modalSubmitActionLabel('Unduh Excel (.xlsx)')
                ->form([
                    TextInput::make('year')
                        ->label('Tahun Kuota Cuti')
                        ->numeric()
                        ->default((int) date('Y'))
                        ->required(),
                    Select::make('gender')
                        ->label('Filter Jenis Kelamin')
                        ->options([
                            'all' => 'Semua Pegawai (Laki-laki & Perempuan)',
                            'Male' => 'Hanya Laki-laki (L)',
                            'Female' => 'Hanya Perempuan (P)',
                        ])
                        ->default('all')
                        ->required(),
                    Select::make('status')
                        ->label('Status Kepegawaian')
                        ->options([
                            'Active' => 'Pegawai Aktif Saja',
                            'all' => 'Semua Status Pegawai',
                        ])
                        ->default('Active')
                        ->required(),
                ])
                ->action(function (array $data): StreamedResponse {
                    $year = (int) ($data['year'] ?? date('Y'));
                    $genderFilter = $data['gender'] ?? 'all';
                    $statusFilter = $data['status'] ?? 'Active';

                    $employeesQuery = Employee::accessibleBy()
                        ->with([
                            'branch',
                            'position',
                            'leaveBalances' => fn ($q) => $q->where('year', $year)->with('leaveType'),
                        ])
                        ->orderBy('nip');

                    if ($genderFilter !== 'all') {
                        $employeesQuery->where('gender', $genderFilter);
                    }

                    if ($statusFilter !== 'all') {
                        $employeesQuery->where('status', $statusFilter);
                    }

                    $employees = $employeesQuery->get();
                    $leaveTypes = LeaveType::where('is_active', true)->orderBy('id')->get();

                    $headers = [
                        ['label' => 'No'],
                        ['label' => 'NIP'],
                        ['label' => 'Nama Pegawai'],
                        ['label' => 'L/P'],
                        ['label' => 'Unit Kantor'],
                        ['label' => 'Jabatan'],
                    ];

                    foreach ($leaveTypes as $lt) {
                        $shortLabel = match ($lt->code) {
                            'MATERNITY' => 'Melahirkan (Khusus P)',
                            'PATERNITY' => 'Dampingi Istri (Khusus L)',
                            'BLOCK_LEAVE' => 'Block Leave OJK',
                            default => $lt->name,
                        };
                        $headers[] = ['label' => "{$shortLabel} (Sisa/Kuota)"];
                    }

                    $headers[] = ['label' => 'Total Terpakai (Hari)'];
                    $headers[] = ['label' => 'Sisa Cuti Tahunan (Hari)'];

                    $rows = [];
                    $totalUsedAll = 0;
                    $totalAnnualRemaining = 0;
                    $maleCount = 0;
                    $femaleCount = 0;

                    foreach ($employees as $idx => $emp) {
                        $isMale = LeaveType::normalizeGender($emp->gender) === 'male';
                        $isFemale = LeaveType::normalizeGender($emp->gender) === 'female';
                        if ($isMale) {
                            $maleCount++;
                        } elseif ($isFemale) {
                            $femaleCount++;
                        }

                        $balancesByType = $emp->leaveBalances->keyBy('leave_type_id');
                        $empUsedTotal = 0;
                        $empAnnualRemaining = 0;

                        $row = [
                            ['value' => $idx + 1, 'class' => 'td-center'],
                            ['value' => $emp->nip ?? '-', 'class' => 'td-nip'],
                            ['value' => $emp->full_name ?? '-', 'class' => 'td-bold'],
                            ['value' => $isFemale ? 'P' : 'L', 'class' => 'td-center td-bold'],
                            ['value' => $emp->branch?->name ?? 'Kantor Pusat'],
                            ['value' => $emp->position?->name ?? '-'],
                        ];

                        foreach ($leaveTypes as $lt) {
                            if (! $lt->isAllowedForGender($emp->gender)) {
                                $row[] = ['value' => '-', 'class' => 'td-center'];
                                continue;
                            }

                            $bal = $balancesByType->get($lt->id);
                            $initial = $bal ? (int) $bal->initial_quota : (int) $lt->default_days;
                            $used = $bal ? (int) $bal->used_quota : 0;
                            $remaining = $bal ? (int) $bal->remaining_quota : $initial;

                            $empUsedTotal += $used;
                            if ($lt->code === 'ANNUAL') {
                                $empAnnualRemaining = $remaining;
                            }

                            $cellClass = $used > 0 ? 'td-center td-bold' : 'td-center';
                            $row[] = ['value' => "{$remaining} / {$initial}", 'class' => $cellClass];
                        }

                        $totalUsedAll += $empUsedTotal;
                        $totalAnnualRemaining += $empAnnualRemaining;

                        $row[] = ['value' => $empUsedTotal, 'class' => 'td-num'];
                        $row[] = ['value' => $empAnnualRemaining, 'class' => 'td-num td-bold'];

                        $rows[] = $row;
                    }

                    $summaryRow = [
                        ['value' => '', 'class' => 'td-center'],
                        ['value' => 'TOTAL', 'class' => 'td-center'],
                        ['value' => "{$employees->count()} Pegawai ({$maleCount} L / {$femaleCount} P)"],
                        ['value' => '', 'class' => 'td-center'],
                        ['value' => ''],
                        ['value' => ''],
                    ];

                    foreach ($leaveTypes as $lt) {
                        $summaryRow[] = ['value' => '', 'class' => 'td-center'];
                    }

                    $summaryRow[] = ['value' => $totalUsedAll, 'class' => 'td-num'];
                    $summaryRow[] = ['value' => $totalAnnualRemaining, 'class' => 'td-num'];

                    $genderSuffix = $genderFilter === 'all' ? 'Semua' : ($genderFilter === 'Male' ? 'Laki' : 'Perempuan');

                    return BprExcelExport::stream(
                        "Rekap_Saldo_Cuti_BPR_{$year}_{$genderSuffix}.xlsx",
                        'Rekap Saldo Cuti',
                        'REKAPITULASI HAK & SISA SALDO CUTI PEGAWAI',
                        "Tahun {$year} | Gender: {$genderSuffix} | Format: Sisa / Kuota Awal",
                        $headers,
                        $rows,
                        $summaryRow,
                    );
                }),

            CreateAction::make()
                ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
        ];
    }
}
