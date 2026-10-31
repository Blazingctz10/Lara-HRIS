<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'period_month',
        'period_year',
        'basic_salary',
        'position_allowance',
        'transport_allowance',
        'meal_allowance',
        'overtime_pay',
        'reimbursement_pay',
        'allowances',
        'deductions',
        'bpjs_tk_deduction',
        'bpjs_kes_deduction',
        'pph21_deduction',
        'late_penalty_deduction',
        'loan_deduction',
        'net_salary',
        'overtime_hours',
        'late_count',
        'ptkp_status_used',
        'payment_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'period_month' => 'integer',
        'period_year' => 'integer',
        'basic_salary' => 'decimal:2',
        'position_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'meal_allowance' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'reimbursement_pay' => 'decimal:2',
        'allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'bpjs_tk_deduction' => 'decimal:2',
        'bpjs_kes_deduction' => 'decimal:2',
        'pph21_deduction' => 'decimal:2',
        'late_penalty_deduction' => 'decimal:2',
        'loan_deduction' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Scope query untuk keamanan ketat (Zero-Leak Policy)
     * - HR Admin & Super Admin: Memiliki akses kelola seluruh payroll
     * - Direksi: Read-only untuk audit
     * - Pegawai: Hanya bisa melihat slip gaji miliknya sendiri yang berstatus Paid/Approved
     */
    public function scopeAccessibleBy(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->role === 'super_admin' || $user->role === 'hr_admin' || $user->role === 'direksi') {
            return $query;
        }

        // Karyawan biasa hanya bisa melihat slip gaji miliknya sendiri yang sudah dirilis/Paid
        if ($user->employee) {
            return $query->where('employee_id', $user->employee->id)
                ->whereIn('status', ['Approved', 'Paid']);
        }

        return $query->whereRaw('1 = 0');
    }

    /**
     * Hitung total pendapatan kotor (Gross Earnings)
     */
    public function getTotalEarningsAttribute(): float
    {
        return (float) ($this->basic_salary + $this->position_allowance + $this->transport_allowance + $this->meal_allowance + $this->overtime_pay + $this->reimbursement_pay);
    }

    /**
     * Format nama periode (contoh: Oktober 2026)
     */
    public function getPeriodLabelAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $monthName = $months[$this->period_month] ?? "Bulan {$this->period_month}";

        return "{$monthName} {$this->period_year}";
    }
}
