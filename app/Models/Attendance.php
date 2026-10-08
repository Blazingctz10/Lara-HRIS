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
        'late_minutes',
        'penalty_amount',
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
            'late_minutes' => 'integer',
            'penalty_amount' => 'decimal:2',
            'lat_in' => 'decimal:8',
            'long_in' => 'decimal:8',
            'lat_out' => 'decimal:8',
            'long_out' => 'decimal:8',
            'distance_in_meters' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Attendance $attendance) {
            // Kalkulasi keterlambatan & denda Rp 50.000 jika tap masuk di atas jam kerja resmi (08:00 WIB)
            if (! empty($attendance->clock_in) && in_array($attendance->status, ['Present', 'Late'])) {
                $dateStr = \Carbon\Carbon::parse($attendance->date ?? now())->toDateString();
                $timeStr = \Carbon\Carbon::parse($attendance->clock_in)->format('H:i:s');
                $clockInTime = \Carbon\Carbon::parse("{$dateStr} {$timeStr}");
                $standardTime = \Carbon\Carbon::parse($dateStr)->setTime(8, 0, 0);

                if ($clockInTime->gt($standardTime)) {
                    $minutesLate = abs((int) $clockInTime->diffInMinutes($standardTime));
                    if ($minutesLate >= 1) {
                        $attendance->late_minutes = $minutesLate;
                        $attendance->penalty_amount = 50000.00; // Denda Rp 50.000 sesuai regulasi BPR
                        $attendance->status = 'Late';
                    }
                } else {
                    $attendance->late_minutes = 0;
                    $attendance->penalty_amount = 0.00;
                    if ($attendance->status === 'Late') {
                        $attendance->status = 'Present';
                    }
                }
            }
        });
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
