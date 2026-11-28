<?php

namespace App\Filament\Resources\Payrolls\Pages;

use App\Filament\Resources\Payrolls\PayrollResource;
use App\Models\Payroll;
use App\Services\PayrollExcelService;
use App\Support\BprExcelExport;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListPayrolls extends ListRecords
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_payroll_excel')
                ->label('Unduh Rekap Payroll (Excel)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->modalHeading('Unduh Laporan Rekapitulasi Payroll (.xlsx)')
                ->modalDescription('Ekspor rekap slip gaji pegawai lengkap dengan tunjangan, lembur, potongan BPJS/PPh 21, denda keterlambatan, dan Take-Home Pay.')
                ->modalSubmitActionLabel('Unduh Excel (.xlsx)')
                ->form([
                    Grid::make(3)->schema([
                        Select::make('month')
                            ->label('Bulan')
                            ->options([
                                1 => '01 - Januari', 2 => '02 - Februari', 3 => '03 - Maret',
                                4 => '04 - April', 5 => '05 - Mei', 6 => '06 - Juni',
                                7 => '07 - Juli', 8 => '08 - Agustus', 9 => '09 - September',
                                10 => '10 - Oktober', 11 => '11 - November', 12 => '12 - Desember',
                            ])
                            ->default((int) date('m'))
                            ->required(),
                        TextInput::make('year')
                            ->label('Tahun')
                            ->numeric()
                            ->default((int) date('Y'))
                            ->required(),
                        Select::make('status')
                            ->label('Filter Status')
                            ->options([
                                'all' => 'Semua Status',
                                'Paid' => 'Sudah Dibayar (Paid)',
                                'Draft' => 'Draft',
                            ])
                            ->default('all')
                            ->required(),
                    ]),
                ])
                ->action(function (array $data): StreamedResponse {
                    $month = (int) $data['month'];
                    $year = (int) $data['year'];
                    $status = $data['status'] ?? 'all';

                    $query = Payroll::accessibleBy()
                        ->with(['employee.branch', 'employee.position'])
                        ->where('period_month', $month)
                        ->where('period_year', $year);

                    if ($status !== 'all') {
                        $query->where('status', $status);
                    }

                    $records = $query->join('employees', 'employees.id', '=', 'payrolls.employee_id')
                        ->select('payrolls.*')
                        ->orderBy('employees.branch_id')
                        ->orderBy('employees.nip')
                        ->get();
                    $periodLabel = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
                    $filename = sprintf('Rekap_Payroll_BPR_%04d_%02d.xlsx', $year, $month);

                    $totals = [
                        'basic' => 0.0, 'allowances' => 0.0, 'extras' => 0.0, 'deductions' => 0.0, 'net' => 0.0,
                    ];

                    $rows = [];
                    foreach ($records as $idx => $p) {
                        $emp = $p->employee;
                        $fixedAllowance = (float) $p->position_allowance + (float) $p->transport_allowance + (float) $p->meal_allowance;
                        $extras = (float) $p->overtime_pay;
                        $totalDeductions = (float) $p->bpjs_tk_deduction + (float) $p->bpjs_kes_deduction + (float) $p->pph21_deduction + (float) $p->late_penalty_deduction;
                        $rekening = $emp?->bank_bpr_account ?: ($emp?->bank_account_number ?: '-');

                        $totals['basic'] += (float) $p->basic_salary;
                        $totals['allowances'] += $fixedAllowance;
                        $totals['extras'] += $extras;
                        $totals['deductions'] += $totalDeductions;
                        $totals['net'] += (float) $p->net_salary;

                        $rows[] = [
                            ['value' => $idx + 1, 'class' => 'td-center'],
                            ['value' => $emp?->nip ?? '-', 'class' => 'td-nip'],
                            ['value' => $emp?->full_name ?? '-', 'class' => 'td-bold'],
                            ['value' => $emp?->branch?->name ?? 'Kantor Pusat'],
                            ['value' => number_format((float) $p->basic_salary, 0, '.', ''), 'class' => 'td-rp'],
                            ['value' => number_format($fixedAllowance, 0, '.', ''), 'class' => 'td-rp'],
                            ['value' => number_format($extras, 0, '.', ''), 'class' => 'td-rp'],
                            ['value' => number_format($totalDeductions, 0, '.', ''), 'class' => 'td-rp-deduct'],
                            ['value' => number_format((float) $p->net_salary, 0, '.', ''), 'class' => 'td-rp-highlight td-bold'],
                            ['value' => $rekening, 'class' => 'td-text td-center'],
                            ['value' => $p->status, 'class' => $p->status === 'Paid' ? 'badge-ok' : 'badge-warn'],
                        ];
                    }

                    $summaryRow = [
                        ['value' => '', 'class' => 'td-center'],
                        ['value' => 'TOTAL', 'class' => 'td-center'],
                        ['value' => count($records) . ' Pegawai'],
                        ['value' => ''],
                        ['value' => number_format($totals['basic'], 0, '.', ''), 'class' => 'td-rp'],
                        ['value' => number_format($totals['allowances'], 0, '.', ''), 'class' => 'td-rp'],
                        ['value' => number_format($totals['extras'], 0, '.', ''), 'class' => 'td-rp'],
                        ['value' => number_format($totals['deductions'], 0, '.', ''), 'class' => 'td-rp'],
                        ['value' => number_format($totals['net'], 0, '.', ''), 'class' => 'td-rp'],
                        ['value' => ''],
                        ['value' => ''],
                    ];

                    return BprExcelExport::stream(
                        $filename,
                        'Rekap Payroll',
                        'REKAPITULASI PENGGAJIAN (PAYROLL) KARYAWAN',
                        $periodLabel,
                        [
                            ['label' => 'No'],
                            ['label' => 'NIP'],
                            ['label' => 'Nama Pegawai'],
                            ['label' => 'Unit Kantor'],
                            ['label' => 'Gaji Pokok'],
                            ['label' => 'Tunjangan'],
                            ['label' => 'Uang Lembur'],
                            ['label' => 'Total Potongan (BPJS & PPh21)'],
                            ['label' => 'Take-Home Pay (Netto)'],
                            ['label' => 'No. Rekening BPR'],
                            ['label' => 'Status'],
                        ],
                        $rows,
                        $summaryRow,
                        freezeCols: 3,
                    );
                }),

            Action::make('download_payroll_template')
                ->label('Template Excel Payroll')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->visible(fn (): bool => in_array(auth()->user()?->role, ['super_admin', 'hr_admin']))
                ->form([
                    Grid::make(2)->schema([
                        Select::make('month')
                            ->label('Bulan Periode')
                            ->options([
                                1 => '01 - Januari', 2 => '02 - Februari', 3 => '03 - Maret',
                                4 => '04 - April', 5 => '05 - Mei', 6 => '06 - Juni',
                                7 => '07 - Juli', 8 => '08 - Agustus', 9 => '09 - September',
                                10 => '10 - Oktober', 11 => '11 - November', 12 => '12 - Desember',
                            ])
                            ->default((int) date('m'))
                            ->required(),

                        TextInput::make('year')
                            ->label('Tahun Periode')
                            ->numeric()
                            ->default((int) date('Y'))
                            ->required(),
                    ]),
                ])
                ->action(fn (array $data): StreamedResponse => app(PayrollExcelService::class)->downloadTemplate((int) $data['month'], (int) $data['year'])),

            Action::make('import_payroll_excel')
                ->label('Import Payroll (Excel)')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->visible(fn (): bool => in_array(auth()->user()?->role, ['super_admin', 'hr_admin']))
                ->modalHeading('Import Hasil Hitungan Payroll dari Excel HR')
                ->modalDescription('Unggah file Excel (.xlsx / .xls / .csv) hasil perhitungan HR. Sistem akan langsung menyimpan rincian slip gaji tanpa menghitung ulang, lalu mendistribusikannya ke Portal Pegawai.')
                ->modalSubmitActionLabel('Proses & Simpan Slip Gaji')
                ->form([
                    Grid::make(2)->schema([
                        Select::make('month')
                            ->label('Bulan Periode')
                            ->options([
                                1 => '01 - Januari', 2 => '02 - Februari', 3 => '03 - Maret',
                                4 => '04 - April', 5 => '05 - Mei', 6 => '06 - Juni',
                                7 => '07 - Juli', 8 => '08 - Agustus', 9 => '09 - September',
                                10 => '10 - Oktober', 11 => '11 - November', 12 => '12 - Desember',
                            ])
                            ->default((int) date('m'))
                            ->required(),

                        TextInput::make('year')
                            ->label('Tahun Periode')
                            ->numeric()
                            ->default((int) date('Y'))
                            ->required(),
                    ]),

                    FileUpload::make('excel_file')
                        ->label('File Excel Payroll (.xlsx / .xls / .csv)')
                        ->disk('local')
                        ->directory('payroll-imports')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv',
                            'text/plain',
                        ])
                        ->required(),

                    Toggle::make('publish_immediately')
                        ->label('Langsung Rilis ke Portal Pegawai (Status Paid)')
                        ->helperText('Jika aktif, slip gaji langsung dapat dilihat dan dicetak oleh masing-masing pegawai di Portal.')
                        ->default(false),
                ])
                ->action(function (array $data): void {
                    $path = Storage::disk('local')->path($data['excel_file']);
                    $result = app(PayrollExcelService::class)->importFromExcel(
                        $path,
                        (int) $data['month'],
                        (int) $data['year'],
                        (bool) ($data['publish_immediately'] ?? false)
                    );

                    Storage::disk('local')->delete($data['excel_file']);

                    Notification::make()
                        ->title($result['success'] ? 'Import Payroll Berhasil' : 'Import Payroll Gagal')
                        ->body($result['message'])
                        ->{$result['success'] ? 'success' : 'danger'}()
                        ->send();
                }),

            Action::make('release_all_drafts')
                ->label('Rilis Semua Slip Draft')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->visible(fn (): bool => in_array(auth()->user()?->role, ['super_admin', 'hr_admin']))
                ->requiresConfirmation()
                ->modalHeading('Rilis & Distribusikan Semua Slip Gaji Draft')
                ->modalDescription('Seluruh slip gaji berstatus Draft pada periode yang dipilih akan diubah menjadi Paid sehingga langsung muncul di Portal masing-masing pegawai.')
                ->form([
                    Grid::make(2)->schema([
                        Select::make('month')
                            ->label('Bulan Periode')
                            ->options([
                                1 => '01 - Januari', 2 => '02 - Februari', 3 => '03 - Maret',
                                4 => '04 - April', 5 => '05 - Mei', 6 => '06 - Juni',
                                7 => '07 - Juli', 8 => '08 - Agustus', 9 => '09 - September',
                                10 => '10 - Oktober', 11 => '11 - November', 12 => '12 - Desember',
                            ])
                            ->default((int) date('m'))
                            ->required(),

                        TextInput::make('year')
                            ->label('Tahun Periode')
                            ->numeric()
                            ->default((int) date('Y'))
                            ->required(),
                    ]),
                ])
                ->action(function (array $data): void {
                    $released = app(PayrollExcelService::class)->releasePeriod((int) $data['month'], (int) $data['year']);
                    Notification::make()
                        ->title('Distribusi Slip Gaji Selesai')
                        ->body("{$released} slip gaji berhasil dirilis ke Portal Pegawai.")
                        ->success()
                        ->send();
                }),

            CreateAction::make()
                ->label('Input Manual Gaji')
                ->visible(fn (): bool => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
        ];
    }
}
