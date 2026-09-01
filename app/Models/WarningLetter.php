<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarningLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'level',
        'letter_number',
        'reason',
        'issued_date',
        'expiry_date',
        'status',
        'attachment_url',
        'issued_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'issued_date' => 'date:Y-m-d',
            'expiry_date' => 'date:Y-m-d',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
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
