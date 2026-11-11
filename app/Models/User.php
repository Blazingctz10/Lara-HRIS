<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar_url',
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

    /**
     * Dapatkan URL avatar pengguna untuk panel Filament (Lokal & Cepat).
     */
    public function getFilamentAvatarUrl(): ?string
    {
        $photo = $this->avatar_url ?? $this->employee?->photo_url;

        if ($photo && Storage::disk('public')->exists($photo)) {
            return Storage::disk('public')->url($photo);
        }

        return static::generateInitialsAvatarSvg($this->name);
    }

    /**
     * Hasilkan Avatar SVG Inisial Nama Berbasis Data URI (0ms latency, zero external network call).
     */
    public static function generateInitialsAvatarSvg(?string $name): string
    {
        $name = trim((string) $name);
        $words = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $char = mb_substr($w, 0, 1);
            if (preg_match('/[\p{L}\p{N}]/u', $char)) {
                $initials .= mb_strtoupper($char);
            }
        }

        if ($initials === '') {
            $initials = 'BR';
        }

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">'
            . '<rect width="100%" height="100%" rx="50%" fill="#0E6838"/>'
            . '<text x="50%" y="54%" font-family="Plus Jakarta Sans, -apple-system, sans-serif" font-size="38" font-weight="700" fill="#FFFFFF" text-anchor="middle" dominant-baseline="middle">'
            . htmlspecialchars($initials, ENT_QUOTES | ENT_XML1, 'UTF-8')
            . '</text>'
            . '</svg>';

        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }
}
