<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Overtime;
use App\Models\Payroll;
use App\Models\Reimbursement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollCalculationService
{
    /**
     * Hitung kalkulasi gaji otomatis seorang pegawai untuk suatu periode bulan & tahun.
     */
    public function calculateForEmployee(Employee $employee, int $month, int $year): array
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = (clone $startDate)->endOfMonth();

        // 1. Gaji Pokok & Tunjangan Standar Berdasarkan Posisi & Grade
        $position = $employee->position;
        $basicSalary = (float) ($position?->min_salary ?? 4500000); // Default UMK Batam jika belum diatur

        // Tunjangan Jabatan & Operasional berdasar Grade Level
        $gradeLevel = $position?->grade_level ?? 'Staff';
        $positionAllowance = match ($gradeLevel) {
            'Division Head', 'Vice President' => 5000000,
            'Manager' => 3000000,
            'Assistant Manager' => 1500000,
            'Supervisor' => 800000,
            'Officer' => 500000,
            default => 250000,
        };

        $transportAllowance = 400000; // Standar transport perbankan
        $mealAllowance = 500000; // Uang makan 25 hari kerja @ 20k

        // 2. Hitung Lembur (Overtime) yang telah disetujui (Approved)
        $approvedOvertimes = Overtime::where('employee_id', $employee->id)
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->whereIn('status', ['Approved_HR', 'Approved_Pincab', 'Paid'])
            ->get();

        $totalOvertimeHours = (int) $approvedOvertimes->sum('duration_hours');
        // Rumus Depnaker 1/173 x Gaji Pokok per jam
        $hourlyRate = round($basicSalary / 173, 2);
        $overtimePay = round($totalOvertimeHours * $hourlyRate * 1.5, 2);

        // 3. Hitung Reimbursement yang sudah disetujui untuk dicairkan bersama payroll
        $reimbursements = Reimbursement::where('employee_id', $employee->id)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->whereIn('status', ['Approved_Pincab', 'Paid'])
            ->get();
        $reimbursementPay = (float) $reimbursements->sum('amount');

        // 4. Hitung Denda Keterlambatan Presensi (Late Penalty)
        // Standar BPR: Rp 50.000 per keterlambatan tanpa dispensasi
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->where('status', 'Late')
            ->get();

        $lateCount = $attendances->count();
        $latePenaltyDeduction = (float) $attendances->sum('late_penalty');
        if ($latePenaltyDeduction <= 0 && $lateCount > 0) {
            $latePenaltyDeduction = $lateCount * 50000;
        }

        // 5. Potongan BPJS Ketenagakerjaan (Karyawan)
        // JHT 2% + JP 1% (Maksimal plafon JP Rp 10.042.300)
        $jpBasis = min($basicSalary, 10042300);
        $bpjsTkDeduction = round(($basicSalary * 0.02) + ($jpBasis * 0.01), 2);

        // 6. Potongan BPJS Kesehatan (Karyawan 1%, Maksimal plafon Rp 12.000.000)
        $kesBasis = min($basicSalary, 12000000);
        $bpjsKesDeduction = round($kesBasis * 0.01, 2);

        // 7. Estimasi PPh 21 Bulanan (Skema Tarif Efektif Rata-Rata / TER Bulanan)
        $grossTaxable = $basicSalary + $positionAllowance + $transportAllowance + $mealAllowance + $overtimePay;
        $ptkpStatus = $employee->ptkp_status ?: 'TK/0';
        $pph21Rate = $this->getMonthlyTerRate($ptkpStatus, $grossTaxable);
        $pph21Deduction = round($grossTaxable * $pph21Rate, 2);

        // 8. Total Komponen
        $totalAllowances = $positionAllowance + $transportAllowance + $mealAllowance + $overtimePay + $reimbursementPay;
        $totalDeductions = $bpjsTkDeduction + $bpjsKesDeduction + $pph21Deduction + $latePenaltyDeduction;
        $netSalary = max(0, ($basicSalary + $totalAllowances) - $totalDeductions);

        return [
            'employee_id' => $employee->id,
            'period_month' => $month,
            'period_year' => $year,
            'basic_salary' => $basicSalary,
            'position_allowance' => $positionAllowance,
            'transport_allowance' => $transportAllowance,
            'meal_allowance' => $mealAllowance,
            'overtime_pay' => $overtimePay,
            'reimbursement_pay' => $reimbursementPay,
            'allowances' => $totalAllowances,
            'deductions' => $totalDeductions,
            'bpjs_tk_deduction' => $bpjsTkDeduction,
            'bpjs_kes_deduction' => $bpjsKesDeduction,
            'pph21_deduction' => $pph21Deduction,
            'late_penalty_deduction' => $latePenaltyDeduction,
            'loan_deduction' => 0,
            'net_salary' => $netSalary,
            'overtime_hours' => $totalOvertimeHours,
            'late_count' => $lateCount,
            'ptkp_status_used' => $ptkpStatus,
            'payment_date' => Carbon::createFromDate($year, $month, 28)->toDateString(),
            'status' => 'Draft',
            'notes' => 'Dihitung otomatis oleh Payroll Engine HRIS BPR',
        ];
    }

    /**
     * Hitung dan simpan massal (Batch Generate) untuk seluruh karyawan aktif
     */
    public function generateBatchPayroll(int $month, int $year): array
    {
        $employees = Employee::where('status', 'Active')->get();
        $generated = 0;
        $updated = 0;

        DB::beginTransaction();
        try {
            foreach ($employees as $employee) {
                $calcData = $this->calculateForEmployee($employee, $month, $year);

                $existing = Payroll::where('employee_id', $employee->id)
                    ->where('period_month', $month)
                    ->where('period_year', $year)
                    ->first();

                if ($existing) {
                    if ($existing->status === 'Draft') {
                        $existing->update($calcData);
                        $updated++;
                    }
                } else {
                    Payroll::create($calcData);
                    $generated++;
                }
            }
            DB::commit();

            return [
                'success' => true,
                'generated' => $generated,
                'updated' => $updated,
                'message' => "Proses kalkulasi payroll selesai: {$generated} slip dibuat baru, {$updated} draf diperbarui.",
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'success' => false,
                'generated' => 0,
                'updated' => 0,
                'message' => 'Gagal memproses kalkulasi payroll: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Tabel Persentase TER Bulanan (Peraturan Pemerintah No. 58 / 2023)
     */
    private function getMonthlyTerRate(string $ptkp, float $gross): float
    {
        // Kategori TER A: TK/0, TK/1, K/0
        // Kategori TER B: TK/2, TK/3, K/1, K/2
        // Kategori TER C: K/3
        $cleanPtkp = strtoupper(trim($ptkp));
        $category = 'A';
        if (in_array($cleanPtkp, ['TK/2', 'TK/3', 'K/1', 'K/2'])) {
            $category = 'B';
        } elseif ($cleanPtkp === 'K/3') {
            $category = 'C';
        }

        if ($category === 'A') {
            if ($gross <= 5400000) return 0.00;
            if ($gross <= 5650000) return 0.0025;
            if ($gross <= 5950000) return 0.005;
            if ($gross <= 6300000) return 0.0075;
            if ($gross <= 6750000) return 0.01;
            if ($gross <= 7500000) return 0.0125;
            if ($gross <= 8550000) return 0.015;
            if ($gross <= 9650000) return 0.0175;
            if ($gross <= 10050000) return 0.02;
            if ($gross <= 10350000) return 0.0225;
            if ($gross <= 10700000) return 0.025;
            if ($gross <= 11050000) return 0.03;
            if ($gross <= 11600000) return 0.035;
            if ($gross <= 12500000) return 0.04;
            if ($gross <= 13750000) return 0.05;
            return 0.06;
        }

        if ($category === 'B') {
            if ($gross <= 6200000) return 0.00;
            if ($gross <= 6500000) return 0.0025;
            if ($gross <= 6850000) return 0.005;
            if ($gross <= 7300000) return 0.0075;
            if ($gross <= 9200000) return 0.01;
            if ($gross <= 10750000) return 0.015;
            if ($gross <= 12500000) return 0.02;
            return 0.03;
        }

        // Kategori C
        if ($gross <= 6600000) return 0.00;
        if ($gross <= 6950000) return 0.0025;
        if ($gross <= 7350000) return 0.005;
        if ($gross <= 7800000) return 0.0075;
        if ($gross <= 9500000) return 0.01;
        return 0.015;
    }
}
