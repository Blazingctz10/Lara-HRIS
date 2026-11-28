<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceDispensation;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\JobApplicant;
use App\Models\LeaveRequest;
use App\Models\Overtime;
use App\Models\Payroll;
use App\Models\Reimbursement;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class HrisDashboardCacheService
{
    public const CACHE_KEY = 'hris:dashboard_metrics:v1';

    public function getCacheDir(): string
    {
        $dir = storage_path('app/hris-cache');
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        return $dir;
    }

    public function getSnapshotPath(): string
    {
        $filename = app()->runningUnitTests()
            ? 'dashboard_metrics_testing.json'
            : 'dashboard_metrics.json';

        return $this->getCacheDir() . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * Hitung seluruh metrik HRIS di belakang layar dan simpan ke storage/app/hris-cache/ + Cache Laravel.
     */
    public function warmAll(): array
    {
        $started = microtime(true);

        $activeEmployees = Employee::where('status', 'Active')->count();
        $totalEmployees = Employee::count();
        $activeUsers = User::whereDoesntHave('employee', fn ($eq) => $eq->whereIn('status', ['Resigned', 'Terminated']))
            ->count();

        $activeBranches = Branch::where('is_active', true)->count();
        $attendanceToday = Attendance::whereDate('date', today())->count();

        $unpaidClaimsQuery = Reimbursement::whereIn('status', ['Pending', 'Approved_Pincab']);
        $unpaidClaimsCount = (clone $unpaidClaimsQuery)->count();
        $unpaidClaimsSum = (float) (clone $unpaidClaimsQuery)->sum('amount');

        $pendingHrLeaves = LeaveRequest::where('status', 'Pending')->count();
        $pendingDireksiLeaves = LeaveRequest::where('status', 'Approved_HR')->count();
        $pendingOvertime = Overtime::where('status', 'Pending')->count();
        $draftPayroll = Payroll::where('status', 'Draft')->count();
        $newApplicants = JobApplicant::whereIn('status', ['Applied', 'Baru'])->count();
        $dispensationCount = AttendanceDispensation::count();

        $payload = [
            'active_employees' => $activeEmployees,
            'total_employees' => $totalEmployees,
            'active_users' => $activeUsers,
            'active_branches' => $activeBranches,
            'attendance_today' => $attendanceToday,
            'unpaid_claims_count' => $unpaidClaimsCount,
            'unpaid_claims_sum' => $unpaidClaimsSum,
            'pending_hr_leaves' => $pendingHrLeaves,
            'pending_direksi_leaves' => $pendingDireksiLeaves,
            'pending_overtime' => $pendingOvertime,
            'draft_payroll' => $draftPayroll,
            'new_applicants' => $newApplicants,
            'dispensation_count' => $dispensationCount,
            'warmed_at' => now()->toIso8601String(),
            'duration_ms' => round((microtime(true) - $started) * 1000, 1),
        ];

        File::put(
            $this->getSnapshotPath(),
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
        Cache::put(self::CACHE_KEY, $payload, now()->addMinutes(12));

        return $payload;
    }

    /**
     * Ambil metrik dari cache/folder hris-cache (jika belum ada atau kedaluwarsa > 10 menit, hitung sekali).
     */
    public function getMetrics(): array
    {
        $cached = Cache::get(self::CACHE_KEY);
        if (is_array($cached) && ! empty($cached['warmed_at'])) {
            return $cached;
        }

        $path = $this->getSnapshotPath();
        if (File::exists($path)) {
            $mtime = File::lastModified($path);
            if ($mtime >= now()->subMinutes(10)->timestamp) {
                $decoded = json_decode((string) File::get($path), true);
                if (is_array($decoded) && ! empty($decoded['warmed_at'])) {
                    Cache::put(self::CACHE_KEY, $decoded, now()->addMinutes(12));

                    return $decoded;
                }
            }
        }

        return $this->warmAll();
    }
}
