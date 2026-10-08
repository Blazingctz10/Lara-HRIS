<?php

namespace App\Models;

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
}
