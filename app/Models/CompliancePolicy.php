<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CompliancePolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number',
        'title',
        'category',
        'summary',
        'attachment_path',
        'file_size',
        'effective_date',
        'target_audience',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'apu_ppt' => 'APU & PPT (Regulasi OJK)',
            'code_of_conduct' => 'Kode Etik & Anti-Fraud',
            'sop_operation' => 'SOP Operasional & Layanan Kas',
            'risk_management' => 'Manajemen Risiko & Keamanan',
            'company_regulation' => 'Peraturan Perusahaan & Disiplin',
            default => 'Kebijakan Lainnya',
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'apu_ppt' => 'danger',
            'code_of_conduct' => 'warning',
            'sop_operation' => 'success',
            'risk_management' => 'info',
            'company_regulation' => 'primary',
            default => 'gray',
        };
    }

    public function getTargetAudienceLabelAttribute(): string
    {
        return match ($this->target_audience) {
            'all' => 'Seluruh Karyawan (Nasional)',
            'branch_ops' => 'Operasional & Layanan Cabang',
            'executive_only' => 'Khusus Pejabat Eksekutif & Pincab',
            default => 'Seluruh Karyawan',
        };
    }

    public function getAttachmentUrlAttribute(): string
    {
        if ($this->attachment_path && Storage::disk('public')->exists($this->attachment_path)) {
            return Storage::disk('public')->url($this->attachment_path);
        }

        return '#';
    }

    /**
     * Scope untuk kebijakan yang dapat diakses pengguna
     */
    public function scopeAccessibleBy(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        // Super Admin, HR Admin, Direksi dapat melihat semua dokumen
        if ($user->isSuperAdmin() || $user->isHrAdmin() || $user->isDireksi()) {
            return $query;
        }

        // Pimpinan Cabang dapat melihat audience all, branch_ops, dan executive_only
        if ($user->isBranchManager()) {
            return $query->where('status', 'active')
                ->whereIn('target_audience', ['all', 'branch_ops', 'executive_only']);
        }

        // Karyawan biasa / staf cabang
        return $query->where('status', 'active')
            ->whereIn('target_audience', ['all', 'branch_ops']);
    }
}

