<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceDispensation extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'dispensation_type',
        'custom_reason',
        'description',
        'reference_contact',
        'attachment',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    protected static function booted(): void
    {
        static::saved(function (AttendanceDispensation $dispensation) {
            // Sinkronisasi otomatis ke log kehadiran utama agar tidak bolong/alpa
            $reasonText = ! empty($dispensation->custom_reason)
                ? ($dispensation->dispensation_type === 'Lainnya / Isi Sendiri'
                    ? $dispensation->custom_reason
                    : "{$dispensation->dispensation_type} - {$dispensation->custom_reason}")
                : $dispensation->dispensation_type;

            $noteContent = "[Dispensasi HRD/GA: {$reasonText}] " . ($dispensation->description ?: '');

            Attendance::updateOrCreate(
                [
                    'employee_id' => $dispensation->employee_id,
                    'date' => \Carbon\Carbon::parse($dispensation->date)->toDateString(),
                ],
                [
                    'status' => 'Excused',
                    'notes' => trim($noteContent),
                ]
            );
        });
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
