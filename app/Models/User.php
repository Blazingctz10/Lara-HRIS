<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'branch_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * Determine which Filament panels the user can access.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return in_array($this->role, ['super_admin', 'hr_admin', 'branch_manager', 'direksi']);
        }

        if ($panel->getId() === 'portal') {
            return true; // Semua role / karyawan dapat mengakses portal ESS
        }

        return false;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isDireksi(): bool
    {
        return in_array($this->role, ['direksi', 'super_admin']);
    }

    public function isHrAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'hr_admin', 'direksi']);
    }

    public function isBranchManager(): bool
    {
        return $this->role === 'branch_manager';
    }
}
