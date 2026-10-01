<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePerformanceAppraisal extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'period_year',
        'letter_grade',
        'score',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'period_year' => 'integer',
            'score' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
