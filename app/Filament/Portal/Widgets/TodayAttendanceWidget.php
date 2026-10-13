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
            return [
                'type' => 'present',
                'badge' => 'Status: ' . ($att->status === 'Late' ? 'Terlambat (' . ($att->late_minutes ?? 0) . ' Mnt)' : 'Hadir Tepat Waktu'),
                'notes' => $att->notes ?: 'Tersinkronisasi dari berkas presensi cabang',
            ];
        }

        return [
            'type' => 'normal',
            'badge' => 'Data Terpadu (Mesin, Cuti & Logbook)',
            'notes' => '',
        ];
    }
}
