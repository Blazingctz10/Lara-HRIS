<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FingerprintDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'model',
        'serial_number',
        'branch_id',
        'ip_address',
        'port',
        'comm_key',
        'status',
        'last_sync_at',
        'total_logs_count',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'total_logs_count' => 'integer',
            'is_active' => 'boolean',
            'last_sync_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
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
            return $query->where('branch_id', $user->branch_id);
        }

        return $query;
    }
}
