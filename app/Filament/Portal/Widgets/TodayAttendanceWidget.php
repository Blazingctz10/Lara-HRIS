<?php

namespace App\Filament\Portal\Widgets;

use App\Models\Attendance;
use Filament\Widgets\Widget;

class TodayAttendanceWidget extends Widget
{
    protected string $view = 'filament.portal.widgets.today-attendance-widget';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public function getTodayAttendance(): ?Attendance
    {
        $employee = auth()->user()?->employee;
        if (! $employee) {
            return null;
        }

        return Attendance::where('employee_id', $employee->id)
            ->whereDate('date', now())
            ->first();
    }

    public function getTodaySchedule(): array
    {
        $dayOfWeek = now()->dayOfWeek; // 0 = Sun, 1 = Mon, ..., 5 = Fri, 6 = Sat

        return match ($dayOfWeek) {
            0 => [
                'day_name' => 'Minggu',
                'hours' => 'Libur Operasional',
                'is_work_day' => false,
                'badge' => 'Libur Operasional',
            ],
            5 => [
                'day_name' => 'Jumat',
                'hours' => '08:00 - 16:30 WIB',
                'is_work_day' => true,
                'badge' => '08:00 - 16:30 WIB (Jumat)',
            ],
            6 => [
                'day_name' => 'Sabtu',
                'hours' => '08:00 - 13:00 WIB',
                'is_work_day' => true,
                'badge' => '08:00 - 13:00 WIB (Sabtu)',
            ],
            default => [
                'day_name' => now()->translatedFormat('l'),
                'hours' => '08:00 - 16:00 WIB',
                'is_work_day' => true,
                'badge' => '08:00 - 16:00 WIB (Senin - Kamis)',
            ],
        };
    }

    public function getTodayStatusInfo(): array
    {
        $employee = auth()->user()?->employee;
        if (! $employee) {
            return ['type' => 'normal', 'badge' => 'Data Terpadu (Mesin, Cuti & Logbook)', 'notes' => ''];
        }

        $today = now()->toDateString();

        // 1. Cek Cuti Resmi
        $approvedLeave = \App\Models\LeaveRequest::where('employee_id', $employee->id)
            ->where(fn ($q) => $q->where('status', 'LIKE', 'Approved%')->orWhere('status', 'approved'))
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->with('leaveType')
            ->first();

        if ($approvedLeave) {
            return [
                'type' => 'leave',
                'badge' => 'Status: Cuti Resmi (' . ($approvedLeave->leaveType?->name ?? 'Cuti') . ')',
                'notes' => $approvedLeave->reason ?: 'Izin cuti disetujui',
            ];
        }

        // 2. Cek Dispensasi Logbook
        $dispensation = \App\Models\AttendanceDispensation::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if ($dispensation) {
            return [
                'type' => 'dispensation',
                'badge' => 'Status: Izin / Sakit (Dispensasi HRD/GA)',
                'notes' => $dispensation->custom_reason ?: $dispensation->description,
            ];
        }

        // 3. Cek Absensi yang terunggah
        $att = $this->getTodayAttendance();
        if ($att && $att->clock_in) {
            $timeFormatted = \Carbon\Carbon::parse($att->clock_in)->format('H:i');
            $outFormatted = $att->clock_out ? ' - Pulang ' . \Carbon\Carbon::parse($att->clock_out)->format('H:i') : '';
            return [
                'type' => 'present',
                'badge' => 'Status: ' . ($att->status === 'Late' ? "Terlambat ({$att->late_minutes} Mnt)" : 'Hadir') . " ({$timeFormatted} WIB{$outFormatted})",
                'notes' => $att->notes ?: 'Tersinkronisasi dari mesin biometrik cabang',
            ];
        }

        return [
            'type' => 'normal',
            'badge' => 'Presensi Biometrik (Fingerprint Cabang)',
            'notes' => '',
        ];
    }

    public function getLeaveSummary(): array
    {
        $employee = auth()->user()?->employee;
        if (! $employee) {
            return [
                'annual_remaining' => 0,
                'block_remaining' => 0,
                'pending_count' => 0,
            ];
        }

        $balances = \App\Models\EmployeeLeaveBalance::with('leaveType')
            ->where('employee_id', $employee->id)
            ->where('year', now()->year)
            ->get();

        $annual = $balances->firstWhere('leaveType.code', 'ANNUAL');
        $block = $balances->firstWhere('leaveType.code', 'BLOCK_LEAVE');

        $pendingCount = \App\Models\LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'Pending')
            ->count();

        return [
            'annual_remaining' => $annual->remaining_quota ?? 12,
            'block_remaining' => $block->remaining_quota ?? 5,
            'pending_count' => $pendingCount,
        ];
    }

    public function getTenureInfo(): ?string
    {
        $employee = auth()->user()?->employee;
        if (! $employee || ! $employee->join_date) {
            return null;
        }

        $diff = $employee->join_date->diff(now());
        $years = $diff->y;
        $months = $diff->m;

        if ($years > 0 && $months > 0) {
            return "{$years} Tahun {$months} Bulan";
        } elseif ($years > 0) {
            return "{$years} Tahun";
        } elseif ($months > 0) {
            return "{$months} Bulan";
        }

        return 'Baru Bergabung';
    }

    public function getEmploymentStatusLabel(): string
    {
        $employee = auth()->user()?->employee;
        return match ($employee?->employment_status) {
            'Permanent' => 'Karyawan Tetap (PKWTT)',
            'Contract' => 'Karyawan Kontrak (PKWT)',
            'Probation' => 'Masa Percobaan',
            'Internship' => 'Magang',
            default => 'Karyawan Aktif',
        };
    }
}
