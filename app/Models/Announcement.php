<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'priority',
        'content',
        'attachment_url',
        'branch_id',
        'author_id',
        'is_active',
        'published_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'published_at' => 'date',
            'expires_at' => 'date',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope for announcements visible to a user
     */
    public function scopeAccessibleBy(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return $query;
        }

        if ($user->isHrAdmin() || $user->isCompliance()) {
            return $query;
        }

        if ($user->isBranchManager()) {
            return $query->where(function ($q) use ($user) {
                $q->whereNull('branch_id')
                    ->orWhere('branch_id', $user->branch_id);
            });
        }

        return $this->scopePublishedForUser($query, $user);
    }

    /**
     * Scope for active, published, non-expired announcements for portal display
     */
    public function scopePublishedForUser(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?? auth()->user();
        $today = today()->toDateString();
        $branchId = $user?->branch_id ?? $user?->employee?->branch_id;

        $query->where('is_active', true)
            ->whereDate('published_at', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('expires_at')
                    ->orWhereDate('expires_at', '>=', $today);
            });

        if ($user && ($user->isHrAdmin() || $user->isCompliance())) {
            return $query;
        }

        return $query->where(function ($q) use ($branchId) {
            $q->whereNull('branch_id');
            if ($branchId) {
                $q->orWhere('branch_id', $branchId);
            }
        });
    }

    /**
     * Sanitize rich-text announcement HTML while stripping unsafe attributes/protocols.
     */
    public static function sanitizeContent(?string $html): string
    {
        $clean = strip_tags((string) $html, '<p><br><b><strong><i><em><u><ul><ol><li><a>');
        $clean = preg_replace('/\s+on[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean) ?? $clean;
        $clean = preg_replace('/href\s*=\s*(?:"|\')\s*(?:javascript|data|vbscript):[^"\']*(?:"|\')/i', 'href="#"', $clean) ?? $clean;

        return $clean;
    }
}
