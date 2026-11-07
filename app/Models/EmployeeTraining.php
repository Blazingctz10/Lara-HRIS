<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeTraining extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'training_name',
        'schedule',
        'organizer',
        'trainer',
        'location',
        'certificate_file',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Format jadwal pelatihan jika tersimpan dalam serial angka Excel (contoh: 41265)
     */
    public function getScheduleAttribute(?string $value): ?string
    {
        return self::formatScheduleDate($value);
    }

    public function setScheduleAttribute(?string $value): void
    {
        $this->attributes['schedule'] = self::formatScheduleDate($value);
    }

    public static function formatScheduleDate(?string $value): ?string
    {
        if (empty($value)) {
            return $value;
        }

        $trimmed = trim($value);

        // Jika angka murni serial Excel (antara tahun 1955 s/d 2065: 20000 - 60000)
        if (is_numeric($trimmed) && (int) $trimmed > 20000 && (int) $trimmed < 60000) {
            $months = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
                9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
            ];
            $date = Carbon::createFromTimestampUTC(((int) $trimmed - 25569) * 86400);
            $monthName = $months[$date->month] ?? $date->format('M');
            return sprintf('%02d %s %04d', $date->day, $monthName, $date->year);
        }

        return $value;
    }
}
