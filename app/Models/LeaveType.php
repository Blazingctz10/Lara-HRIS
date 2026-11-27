<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'default_days',
        'is_block_leave',
        'min_consecutive_days',
        'max_consecutive_days',
        'gender_restriction',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'default_days' => 'integer',
            'is_block_leave' => 'boolean',
            'min_consecutive_days' => 'integer',
            'max_consecutive_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function balances(): HasMany
    {
        return $this->hasMany(EmployeeLeaveBalance::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /**
     * Normalisasi string gender pegawai menjadi 'male', 'female', atau null.
     */
    public static function normalizeGender(?string $gender): ?string
    {
        if (! $gender) {
            return null;
        }

        $val = strtolower(trim($gender));

        if (in_array($val, ['male', 'laki-laki', 'l', 'pria'], true)) {
            return 'male';
        }

        if (in_array($val, ['female', 'perempuan', 'p', 'wanita'], true)) {
            return 'female';
        }

        return null;
    }

    /**
     * Scope query jenis cuti agar hanya menampilkan yang sesuai dengan gender pegawai.
     */
    public function scopeForEmployeeGender(Builder $query, ?string $gender): Builder
    {
        $normalized = self::normalizeGender($gender);

        if ($normalized === 'male') {
            return $query->where(fn (Builder $q) => $q->whereNull('gender_restriction')->orWhere('gender_restriction', '!=', 'female'));
        }

        if ($normalized === 'female') {
            return $query->where(fn (Builder $q) => $q->whereNull('gender_restriction')->orWhere('gender_restriction', '!=', 'male'));
        }

        return $query;
    }

    /**
     * Cek apakah jenis cuti ini berlaku untuk gender pegawai tertentu.
     */
    public function isAllowedForGender(?string $gender): bool
    {
        $normalized = self::normalizeGender($gender);

        if (! $this->gender_restriction || ! $normalized) {
            return true;
        }

        return strtolower($this->gender_restriction) === $normalized;
    }
}
