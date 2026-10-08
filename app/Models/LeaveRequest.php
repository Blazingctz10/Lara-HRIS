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
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'leave_address',
        'contact_phone',
        'attachment_url',
        'status',
        'approved_by',
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

        if ($user->isBranchManager() && $user->branch_id) {
            return $query->whereHas('employee', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }

        if ($user->employee) {
            return $query->where('employee_id', $user->employee->id);
        }

        return $query;
    }
}
