<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'leave_type_custom',
        'division_category',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'leave_address',
        'contact_phone',
        'attachment_url',
        'status',
        'approved_by',
        'approved_leader_by',
        'leader_signed_at',
        'leader_signature_name',
        'direksi_signed_at',
        'direksi_signature_name',
        'acc_at',
        'approved_hrd_by',
        'approved_komisaris_by',
        'rejection_reason',
        'komisaris_notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_days' => 'integer',
            'leader_signed_at' => 'datetime',
            'direksi_signed_at' => 'datetime',
            'acc_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approverHrd(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_hrd_by');
    }

    public function approverKomisaris(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_komisaris_by');
    }

    public function approverLeader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_leader_by');
    }

    public static function divisionCategoryOptions(): array
    {
        return [
            'operasional' => 'Div. Operasional',
            'kredit_support' => 'Div. Kredit Support',
            'bisnis' => 'Manager Bisnis',
            'cabang' => 'Bagian Cabang',
        ];
    }

    public static function resolveDivisionCategory(?Employee $employee): string
    {
        if (! $employee) {
            return 'operasional';
        }

        $branchName = mb_strtolower((string) ($employee->branch?->name ?? ''));
        if ($branchName !== '' && ! str_contains($branchName, 'pusat')) {
            return 'cabang';
        }

        $deptCode = mb_strtoupper((string) ($employee->department?->code ?? ''));
        $deptName = mb_strtolower((string) ($employee->department?->name ?? ''));
        $posName = mb_strtolower((string) ($employee->position?->name ?? ''));

        if ($deptCode === 'LEG01' || str_contains($posName, 'support') || str_contains($posName, 'admin kredit') || str_contains($deptName, 'legal')) {
            return 'kredit_support';
        }

        if ($deptCode === 'BKP01' || str_contains($deptName, 'bisnis') || str_contains($posName, 'bisnis') || str_contains($posName, 'marketing') || str_contains($posName, 'ao')) {
            return 'bisnis';
        }

        return 'operasional';
    }

    public function getDivisionCategoryLabelAttribute(): string
    {
        return self::divisionCategoryOptions()[$this->division_category] ?? 'Div. Operasional';
    }

    public function getLeaveTypeDisplayAttribute(): string
    {
        return filled($this->leave_type_custom)
            ? (string) $this->leave_type_custom
            : ($this->leaveType?->name ?? 'Pengajuan Cuti');
    }

    /**
     * Cek apakah pemohon adalah jajaran Direksi
     */
    public function isDireksiRequest(): bool
    {
        return $this->employee?->user?->role === 'direksi';
    }

    /**
     * Aturan Direksi BPR Banda Raya:
     * Cuti sebentar (<= 2 hari) cukup disetujui HRD.
     * Cuti lama (> 2 hari) wajib disetujui oleh Komisaris Utama / Dewan Komisaris.
     */
    public function requiresKomisarisApproval(): bool
    {
        return $this->isDireksiRequest() && $this->total_days > 2;
    }

    /**
     * Branch Scoping for Leave Requests
     */
    public function scopeAccessibleBy(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return $query;
        }

        if ($user->isHrAdmin()) {
            return $query;
        }

        if ($user->isDivisionLeader()) {
            if ($user->division_scope) {
                return $query->where('division_category', $user->division_scope);
            }

            return $query;
        }

        if ($user->isBranchManager()) {
            $branchId = $user->branch_id ?? $user->employee?->branch_id;
            return $query->whereHas('employee', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId ?: 0);
            });
        }

        if ($user->employee) {
            return $query->where('employee_id', $user->employee->id);
        }

        // Fail-safe: Non-admin users without an employee profile cannot see any leave requests
        return $query->whereRaw('1 = 0');
    }
}
