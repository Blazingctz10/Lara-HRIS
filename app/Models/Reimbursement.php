<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reimbursement extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'title',
        'category',
        'date',
        'amount',
        'description',
        'receipt_photo',
        'status',
        'approved_by',
        'payment_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
            'payment_date' => 'date:Y-m-d',
            'amount' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeAccessibleBy(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return $query;
        }

        if ($user->isHrAdmin()) {
            return $query;
        }

        if ($user->isBranchManager()) {
            $branchId = $user->branch_id ?? $user->employee?->branch_id;
            return $query->whereHas('employee', fn ($q) => $q->where('branch_id', $branchId ?: 0));
        }

        if ($user->employee) {
            return $query->where('employee_id', $user->employee->id);
        }

        // Fail-safe: Non-admin users without an employee profile cannot see any reimbursements
        return $query->whereRaw('1 = 0');
    }
}
