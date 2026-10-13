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

    public function isLateArrivalDispensation(): bool
    {
        $text = strtolower(
            ($this->dispensation_type ?? '') . ' ' .
            ($this->custom_reason ?? '') . ' ' .
            ($this->description ?? '')
        );

        return str_contains($text, 'telat')
            || str_contains($text, 'terlambat')
            || str_contains($text, 'siang')
            || str_contains($text, 'keterlambatan');
    }

    protected static function booted(): void
    {
        static::saved(function (AttendanceDispensation $dispensation) {
            // Sinkronisasi otomatis ke log kehadiran utama
            $reasonText = ! empty($dispensation->custom_reason)
                ? ($dispensation->dispensation_type === 'Lainnya / Isi Sendiri'
                    ? $dispensation->custom_reason
                    : "{$dispensation->dispensation_type} - {$dispensation->custom_reason}")
                : $dispensation->dispensation_type;

            $dateString = \Carbon\Carbon::parse($dispensation->date)->toDateString();
            $existing = Attendance::where('employee_id', $dispensation->employee_id)
                ->where('date', $dateString)
                ->first();

            $isLate = $dispensation->isLateArrivalDispensation();

            if ($existing && $existing->clock_in) {
                // Mesin presensi sudah merekam jam masuk (pegawai hadir di kantor)
                if ($isLate) {
                    $existing->update([
                        'status' => 'Present', // Tetap terhitung Hadir
                        'penalty_amount' => 0, // Denda keterlambatan ditiadakan karena dispensasi sah
                        'notes' => "[Dispensasi Telat: {$reasonText}] Jam masuk " . \Carbon\Carbon::parse($existing->clock_in)->format('H:i') . " tercatat. Status Hadir (Denda ditiadakan). " . ($dispensation->description ?: ''),
                    ]);
                } else {
                    $existing->update([
                        'status' => 'Excused',
                        'penalty_amount' => 0,
                        'notes' => "[Dispensasi HRD/GA: {$reasonText}] " . ($dispensation->description ?: ''),
                    ]);
                }
            } else {
                // Belum ada rekaman mesin presensi
                Attendance::updateOrCreate(
                    [
                        'employee_id' => $dispensation->employee_id,
                        'date' => $dateString,
                    ],
                    [
                        'status' => 'Excused',
                        'penalty_amount' => 0,
                        'notes' => $isLate
                            ? "[Dispensasi Telat: {$reasonText}] Izin datang terlambat diajukan, menunggu rekaman presensi mesin cabang. " . ($dispensation->description ?: '')
                            : "[Dispensasi HRD/GA: {$reasonText}] " . ($dispensation->description ?: ''),
                    ]
                );
            }
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
