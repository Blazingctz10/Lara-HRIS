<?php

namespace App\Filament\Resources\LeaveRequests\Pages;

use App\Filament\Resources\LeaveRequests\LeaveRequestResource;
use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use App\Support\BprExcelExport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
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
                ->modalHeading('Unduh Laporan Rekap Saldo & Kuota Cuti (.xls)')
                ->modalDescription('Menghasilkan rekap kuota awal, cuti terpakai, dan sisa kuota cuti seluruh pegawai sesuai hak akses Anda.')
                ->modalSubmitActionLabel('Unduh Excel (.xls)')
                ->form([
                    TextInput::make('year')
                        ->label('Tahun Kuota Cuti')
                        ->numeric()
                        ->default((int) date('Y'))
                        ->required(),
                ])
                ->action(function (array $data): StreamedResponse {
                    $year = (int) ($data['year'] ?? date('Y'));
                    $accessibleEmployeeIds = Employee::accessibleBy()->pluck('id');

                    $balances = EmployeeLeaveBalance::with(['employee.branch', 'employee.position', 'leaveType'])
                        ->whereIn('employee_id', $accessibleEmployeeIds)
                        ->where('year', $year)
                        ->orderBy('employee_id')
                        ->orderBy('leave_type_id')
                        ->get();

                    $rows = [];
                    $totalInitial = 0;
                    $totalUsed = 0;
                    $totalRemaining = 0;

                    foreach ($balances as $idx => $bal) {
                        $emp = $bal->employee;
                        $totalInitial += (int) $bal->initial_quota;
                        $totalUsed += (int) $bal->used_quota;
                        $totalRemaining += (int) $bal->remaining_quota;

                        $rows[] = [
                            ['value' => $idx + 1, 'class' => 'td-center'],
                            ['value' => $emp?->nip ?? '-', 'class' => 'td-nip'],
                            ['value' => $emp?->full_name ?? '-', 'class' => 'td-bold'],
                            ['value' => $emp?->branch?->name ?? 'Kantor Pusat'],
                            ['value' => $emp?->position?->name ?? '-'],
                            ['value' => $bal->leaveType?->name ?? 'Cuti'],
                            ['value' => $bal->year, 'class' => 'td-center'],
                            ['value' => (int) $bal->initial_quota, 'class' => 'td-num'],
                            ['value' => (int) $bal->used_quota, 'class' => 'td-num'],
                            ['value' => (int) $bal->remaining_quota, 'class' => 'td-num td-bold'],
                        ];
                    }

                    $summaryRow = [
                        ['value' => '', 'class' => 'td-center'],
                        ['value' => 'TOTAL', 'class' => 'td-center'],
                        ['value' => count($balances) . ' Baris Saldo'],
                        ['value' => ''],
                        ['value' => ''],
                        ['value' => ''],
                        ['value' => (string) $year, 'class' => 'td-center'],
                        ['value' => $totalInitial, 'class' => 'td-num'],
                        ['value' => $totalUsed, 'class' => 'td-num'],
                        ['value' => $totalRemaining, 'class' => 'td-num'],
                    ];

                    return BprExcelExport::stream(
                        "Rekap_Saldo_Cuti_BPR_{$year}.xls",
                        'Rekap Saldo Cuti',
                        'REKAPITULASI HAK & SISA SALDO CUTI PEGAWAI',
                        "Tahun {$year}",
                        [
                            ['label' => 'No'], ['label' => 'NIP'], ['label' => 'Nama Pegawai'], ['label' => 'Unit Kantor'],
                            ['label' => 'Jabatan'], ['label' => 'Jenis Cuti'], ['label' => 'Tahun'],
                            ['label' => 'Kuota Awal (Hari)'], ['label' => 'Terpakai (Hari)'], ['label' => 'Sisa Kuota (Hari)'],
                        ],
                        $rows,
                        $summaryRow,
                    );
                }),

            CreateAction::make()
                ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
        ];
    }
}
