<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareerMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'type',
        'from_branch_id',
        'to_branch_id',
        'from_position_id',
        'to_position_id',
        'effective_date',
        'sk_number',
        'sk_attachment',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date:Y-m-d',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function fromBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function fromPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'from_position_id');
    }

    public function toPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'to_position_id');
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
