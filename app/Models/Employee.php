<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nip',
        'nik',
        'full_name',
        'email',
        'personal_email',
        'phone',
        'gender',
        'birth_date',
        'birth_place',
        'address',
        'photo_url',
        'branch_id',
        'department_id',
        'position_id',
        'job_effective_date',
        'grade',
        'grade_effective_date',
        'employment_status',
        'join_date',
        'permanent_date',
        'contract_end_date',
        'probation_end_date',
        'end_date',
        'exit_date',
        'exit_reason',
        'status',
        'education_level',
        'education_major',
        'education_institution',
        'ipk',
        'bank_account_number',
        'bank_account_holder',
        'bank_bpr_account',
        'bank_external_name',
        'bank_external_account',
        'npwp',
        'bpjs_tk_number',
        'bpjs_kes_number',
        'ptkp_status',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'join_date' => 'date',
            'job_effective_date' => 'date',
            'grade_effective_date' => 'date',
            'permanent_date' => 'date',
            'contract_end_date' => 'date',
            'probation_end_date' => 'date',
            'exit_date' => 'date',
            'grade' => 'integer',
            'ipk' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Employee $employee) {
            if ($employee->employment_status === 'Permanent') {
                $employee->contract_end_date = null;
                $employee->probation_end_date = null;
                $employee->end_date = null;
            } elseif ($employee->employment_status === 'Contract' || $employee->employment_status === 'Internship') {
                if ($employee->contract_end_date) {
                    $employee->end_date = $employee->contract_end_date;
                } elseif ($employee->end_date) {
                    $employee->contract_end_date = $employee->end_date;
                }
                $employee->probation_end_date = null;
            } elseif ($employee->employment_status === 'Probation') {
                $employee->contract_end_date = null;
                if ($employee->probation_end_date) {
                    $employee->end_date = $employee->probation_end_date;
                } elseif ($employee->end_date) {
                    $employee->probation_end_date = $employee->end_date;
                }
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(EmployeeLeaveBalance::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function overtimes(): HasMany
    {
        return $this->hasMany(Overtime::class);
    }

    public function reimbursements(): HasMany
    {
        return $this->hasMany(Reimbursement::class);
    }

    public function careerMovements(): HasMany
    {
        return $this->hasMany(CareerMovement::class);
    }

    public function warningLetters(): HasMany
    {
        return $this->hasMany(WarningLetter::class);
    }

    public function families(): HasMany
    {
        return $this->hasMany(EmployeeFamily::class);
    }

    public function trainings(): HasMany
    {
        return $this->hasMany(EmployeeTraining::class);
    }

    public function performanceAppraisals(): HasMany
    {
        return $this->hasMany(EmployeePerformanceAppraisal::class)->orderBy('period_year', 'asc');
    }

    /**
     * Hitung masa kerja dalam format "X tahun Y bulan" sesuai standar BPR
     */
    public function getWorkingPeriodAttribute(): string
    {
        if (! $this->join_date) {
            return '-';
        }

        $startDate = \Carbon\Carbon::parse($this->join_date);
        $endDate = $this->exit_date ? \Carbon\Carbon::parse($this->exit_date) : now();

        $diff = $startDate->diff($endDate);
        $years = $diff->y;
        $months = $diff->m;

        $parts = [];
        if ($years > 0) {
            $parts[] = "{$years} tahun";
        }
        if ($months > 0 || $years === 0) {
            $parts[] = "{$months} bulan";
        }

        return implode(' ', $parts);
    }

    /**
     * Hitung usia karyawan otomatis dari tanggal lahir
     */
    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? \Carbon\Carbon::parse($this->birth_date)->age : null;
    }

    /**
     * Branch Scoping: restricts data for Branch Managers to their branch
     */
    public function scopeAccessibleBy(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return $query;
        }

        // Super Admin & HR Admin can see all branches
        if ($user->isHrAdmin()) {
            return $query;
        }

        // Branch manager only sees employees within their branch
        if ($user->isBranchManager() && $user->branch_id) {
            return $query->where('branch_id', $user->branch_id);
        }

        // Regular employee only sees themselves
        if ($user->employee) {
            return $query->where('id', $user->employee->id);
        }

        return $query;
    }
}
