<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Payroll;
use App\Support\BprExcelExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PayrollExcelService
{
    public function __construct(
        private readonly DataMigrationService $migrationService
    ) {}

    /**
     * Unduh template Excel (.xlsx) Payroll resmi berisi daftar pegawai aktif.
     * Ibu HR tinggal mengisi nominal hasil hitungan Excel lalu mengunggahnya kembali.
     */
    public function downloadTemplate(int $month, int $year): StreamedResponse
    {
        $employees = Employee::with(['branch', 'position'])
            ->where('status', 'Active')
            ->orderBy('branch_id')
            ->orderBy('nip')
            ->get();

        $periodLabel = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
        $filename = sprintf('Template_Import_Payroll_BPR_%04d_%02d.xlsx', $year, $month);

        $rows = [];
        foreach ($employees as $emp) {
            $basic = (float) ($emp->basic_salary ?? 0);
            $rows[] = [
                ['value' => $emp->nip, 'class' => 'td-nip'],
                ['value' => $emp->full_name, 'class' => 'td-bold'],
                ['value' => $emp->branch?->name ?? 'Kantor Pusat'],
                ['value' => number_format($basic, 0, '.', ''), 'class' => 'td-rp'],
                ['value' => '0', 'class' => 'td-rp'],
                ['value' => '0', 'class' => 'td-rp'],
                ['value' => '0', 'class' => 'td-rp'],
                ['value' => '0', 'class' => 'td-rp'],
                ['value' => '0', 'class' => 'td-rp-deduct'],
                ['value' => '0', 'class' => 'td-rp-deduct'],
                ['value' => '0', 'class' => 'td-rp-deduct'],
                ['value' => '0', 'class' => 'td-rp-deduct'],
                ['value' => '0', 'class' => 'td-rp-deduct'],
                ['value' => number_format($basic, 0, '.', ''), 'class' => 'td-rp-highlight td-bold'],
                ['value' => '', 'class' => 'td-text'],
            ];
        }

        return BprExcelExport::stream(
            $filename,
            'Input Payroll',
            'TEMPLATE INPUT PAYROLL DARI EXCEL HR (PT BPR BANDA RAYA)',
            $periodLabel,
            [
                ['label' => 'NIP'],
                ['label' => 'Nama Pegawai'],
                ['label' => 'Unit Kantor'],
                ['label' => 'Gaji Pokok'],
                ['label' => 'Tunjangan Jabatan'],
                ['label' => 'Tunjangan Transport'],
                ['label' => 'Uang Makan'],
                ['label' => 'Uang Lembur'],
                ['label' => 'Potongan BPJS TK'],
                ['label' => 'Potongan BPJS Kes'],
                ['label' => 'Potongan PPh 21'],
                ['label' => 'Potongan Denda Telat'],
                ['label' => 'Potongan Kasbon'],
                ['label' => 'Gaji Bersih (THP)'],
                ['label' => 'Catatan'],
            ],
            $rows,
            [],
            freezeCols: 2,
        );
    }

    /**
     * Impor hasil hitungan payroll dari file Excel (.xlsx / .xls / .csv) tanpa menghitung ulang.
     */
    public function importFromExcel(string $filePath, int $month, int $year, bool $publishImmediately = false): array
    {
        $rawRows = $this->migrationService->parseAnyFile($filePath);
        if (count($rawRows) < 2) {
            return [
                'success' => false,
                'imported' => 0,
                'skipped' => 0,
                'message' => '[VAL001] File Excel kosong atau tidak memiliki baris data pegawai.',
            ];
        }

        $headerIndex = null;
        $colMap = [];

        foreach ($rawRows as $idx => $row) {
            $normalized = array_map(fn ($v) => $this->normalizeHeader((string) $v), $row);
            $hasNip = false;
            $hasName = false;
            foreach ($normalized as $colIdx => $h) {
                if ($h === 'nip' || str_contains($h, 'nik_pegawai')) {
                    $hasNip = true;
                    $colMap['nip'] = $colIdx;
                }
                if (str_contains($h, 'nama')) {
                    $hasName = true;
                    $colMap['name'] ??= $colIdx;
                }
            }

            if ($hasNip || $hasName) {
                $headerIndex = $idx;
                $colMap = $this->buildColumnMap($normalized);
                break;
            }
        }

        if ($headerIndex === null) {
            return [
                'success' => false,
                'imported' => 0,
                'skipped' => count($rawRows),
                'message' => '[VAL001] Header kolom NIP / Nama Pegawai tidak ditemukan. Gunakan Template Excel Payroll resmi.',
            ];
        }

        $employeesByNip = Employee::all()->keyBy(fn (Employee $e) => trim((string) $e->nip));
        $employeesByName = Employee::all()->keyBy(fn (Employee $e) => mb_strtolower(trim((string) $e->full_name)));

        $imported = 0;
        $skipped = 0;
        $status = $publishImmediately ? 'Paid' : 'Draft';
        $paymentDate = $publishImmediately ? now()->toDateString() : null;

        DB::beginTransaction();
        try {
            foreach (array_slice($rawRows, $headerIndex + 1) as $row) {
                $nip = isset($colMap['nip']) ? trim((string) ($row[$colMap['nip']] ?? '')) : '';
                $name = isset($colMap['name']) ? trim((string) ($row[$colMap['name']] ?? '')) : '';

                if ($nip === '' && $name === '') {
                    continue;
                }
                if (strtoupper($nip) === 'TOTAL' || str_starts_with(strtoupper($name), 'TOTAL')) {
                    continue;
                }

                $employee = $nip !== '' ? $employeesByNip->get($nip) : null;
                if (! $employee && $name !== '') {
                    $employee = $employeesByName->get(mb_strtolower($name));
                }

                if (! $employee) {
                    $skipped++;
                    continue;
                }

                $basic = $this->parseMoney($row, $colMap['basic'] ?? null);
                $posAllow = $this->parseMoney($row, $colMap['position'] ?? null);
                $transAllow = $this->parseMoney($row, $colMap['transport'] ?? null);
                $mealAllow = $this->parseMoney($row, $colMap['meal'] ?? null);
                $overtimePay = $this->parseMoney($row, $colMap['overtime'] ?? null);

                $bpjsTk = $this->parseMoney($row, $colMap['bpjs_tk'] ?? null);
                $bpjsKes = $this->parseMoney($row, $colMap['bpjs_kes'] ?? null);
                $pph21 = $this->parseMoney($row, $colMap['pph21'] ?? null);
                $latePenalty = $this->parseMoney($row, $colMap['late'] ?? null);
                $loan = $this->parseMoney($row, $colMap['loan'] ?? null);

                $totalAllowances = $posAllow + $transAllow + $mealAllow + $overtimePay;
                $totalDeductions = $bpjsTk + $bpjsKes + $pph21 + $latePenalty + $loan;

                $importedNet = $this->parseMoney($row, $colMap['net'] ?? null);
                $netSalary = $importedNet > 0
                    ? $importedNet
                    : max(0, ($basic + $totalAllowances) - $totalDeductions);

                $notes = isset($colMap['notes']) ? trim((string) ($row[$colMap['notes']] ?? '')) : '';

                Payroll::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'period_month' => $month,
                        'period_year' => $year,
                    ],
                    [
                        'basic_salary' => $basic,
                        'position_allowance' => $posAllow,
                        'transport_allowance' => $transAllow,
                        'meal_allowance' => $mealAllow,
                        'overtime_pay' => $overtimePay,
                        'reimbursement_pay' => 0,
                        'allowances' => $totalAllowances,
                        'deductions' => $totalDeductions,
                        'bpjs_tk_deduction' => $bpjsTk,
                        'bpjs_kes_deduction' => $bpjsKes,
                        'pph21_deduction' => $pph21,
                        'late_penalty_deduction' => $latePenalty,
                        'loan_deduction' => $loan,
                        'net_salary' => $netSalary,
                        'ptkp_status_used' => $employee->ptkp_status ?? 'TK/0',
                        'payment_date' => $paymentDate,
                        'status' => $status,
                        'notes' => $notes !== '' ? $notes : 'Diimpor dari Excel Payroll HR',
                    ]
                );

                $imported++;
            }

            DB::commit();

            return [
                'success' => $imported > 0,
                'imported' => $imported,
                'skipped' => $skipped,
                'message' => $imported > 0
                    ? "Berhasil mengimpor {$imported} slip gaji periode {$month}/{$year} dengan status {$status}" . ($skipped > 0 ? " ({$skipped} baris dilewati karena NIP/Nama tidak ditemukan)." : '.')
                    : '[VAL001] Tidak ada baris pegawai yang cocok dengan master pegawai.',
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'success' => false,
                'imported' => $imported,
                'skipped' => $skipped,
                'message' => '[DB004] Gagal mengimpor Excel Payroll: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Rilis seluruh slip gaji berstatus Draft pada periode tertentu menjadi Paid agar tampil di Portal Pegawai.
     */
    public function releasePeriod(int $month, int $year): int
    {
        return Payroll::where('period_month', $month)
            ->where('period_year', $year)
            ->where('status', 'Draft')
            ->update([
                'status' => 'Paid',
                'payment_date' => now()->toDateString(),
            ]);
    }

    private function normalizeHeader(string $header): string
    {
        $clean = mb_strtolower(trim($header));
        $clean = preg_replace('/[^a-z0-9]+/', '_', $clean) ?? $clean;

        return trim($clean, '_');
    }

    private function buildColumnMap(array $normalizedHeaders): array
    {
        $map = [];
        foreach ($normalizedHeaders as $idx => $h) {
            if ($h === 'nip') {
                $map['nip'] = $idx;
            } elseif (str_contains($h, 'nama')) {
                $map['name'] ??= $idx;
            } elseif (str_contains($h, 'gaji_pokok') || $h === 'gapok') {
                $map['basic'] = $idx;
            } elseif (str_contains($h, 'jabatan')) {
                $map['position'] = $idx;
            } elseif (str_contains($h, 'transport')) {
                $map['transport'] = $idx;
            } elseif (str_contains($h, 'makan')) {
                $map['meal'] = $idx;
            } elseif (str_contains($h, 'lembur') || str_contains($h, 'overtime')) {
                $map['overtime'] = $idx;
            } elseif (str_contains($h, 'bpjs_tk') || str_contains($h, 'ketenagakerjaan')) {
                $map['bpjs_tk'] = $idx;
            } elseif (str_contains($h, 'bpjs_kes') || str_contains($h, 'kesehatan')) {
                $map['bpjs_kes'] = $idx;
            } elseif (str_contains($h, 'pph') || str_contains($h, 'pajak')) {
                $map['pph21'] = $idx;
            } elseif (str_contains($h, 'denda') || str_contains($h, 'telat')) {
                $map['late'] = $idx;
            } elseif (str_contains($h, 'kasbon') || str_contains($h, 'pinjaman')) {
                $map['loan'] = $idx;
            } elseif (str_contains($h, 'bersih') || str_contains($h, 'thp') || str_contains($h, 'take_home')) {
                $map['net'] = $idx;
            } elseif (str_contains($h, 'catatan') || str_contains($h, 'notes') || str_contains($h, 'keterangan')) {
                $map['notes'] = $idx;
            }
        }

        return $map;
    }

    private function parseMoney(array $row, ?int $idx): float
    {
        if ($idx === null || ! array_key_exists($idx, $row)) {
            return 0.0;
        }

        $raw = trim((string) $row[$idx]);
        if ($raw === '' || $raw === '-') {
            return 0.0;
        }

        $clean = preg_replace('/[^0-9,.\-]/', '', $raw) ?? '';
        if ($clean === '') {
            return 0.0;
        }

        // Tangani format Indonesia (1.500.000,00) atau internasional (1500000.00)
        if (str_contains($clean, ',') && str_contains($clean, '.')) {
            $clean = str_replace('.', '', $clean);
            $clean = str_replace(',', '.', $clean);
        } elseif (substr_count($clean, '.') > 1) {
            $clean = str_replace('.', '', $clean);
        } elseif (str_contains($clean, ',') && ! str_contains($clean, '.')) {
            $parts = explode(',', $clean);
            $clean = strlen(end($parts)) <= 2 ? str_replace(',', '.', $clean) : str_replace(',', '', $clean);
        }

        return max(0.0, (float) $clean);
    }
}
