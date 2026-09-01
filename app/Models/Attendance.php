<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'clock_in',
        'clock_out',
        'status',
        'lat_in',
        'long_in',
        'lat_out',
        'long_out',
        'distance_in_meters',
        'proof_photo',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
            'lat_in' => 'decimal:8',
            'long_in' => 'decimal:8',
            'lat_out' => 'decimal:8',
            'long_out' => 'decimal:8',
            'distance_in_meters' => 'integer',
        ];
    }

    public function setDateAttribute($value): void
    {
        $this->attributes['date'] = \Carbon\Carbon::parse($value)->toDateString();
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Branch Scoping for Attendance
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
