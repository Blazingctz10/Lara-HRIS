<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Overtime extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'start_time',
        'end_time',
        'duration_hours',
        'reason',
        'status',
        'approved_by',
        'rejection_note',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
            'duration_hours' => 'decimal:2',
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

        if ($user->isBranchManager() && $user->branch_id) {
            return $query->whereHas('employee', fn ($q) => $q->where('branch_id', $user->branch_id));
        }

        if ($user->employee) {
            return $query->where('employee_id', $user->employee->id);
        }

        return $query;
    }
}
