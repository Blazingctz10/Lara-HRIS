<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeObserver
{
    /**
     * Handle the Employee "created" event.
     */
    public function created(Employee $employee): void
    {
        // 1. Auto-create User account if not yet attached
        if (! $employee->user_id) {
            $user = User::firstOrCreate(
                ['email' => $employee->email],
                [
                    'name' => $employee->full_name,
                    'password' => Hash::make('password'),
                    'role' => 'employee',
                    'branch_id' => $employee->branch_id,
                ]
            );

            $employee->updateQuietly(['user_id' => $user->id]);
        }

        // 2. Auto-initialize Leave Balances for the current year
        $activeLeaveTypes = LeaveType::where('is_active', true)->get();
        $currentYear = now()->year;

        foreach ($activeLeaveTypes as $leaveType) {
            EmployeeLeaveBalance::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'leave_type_id' => $leaveType->id,
                    'year' => $currentYear,
                ],
                [
                    'initial_quota' => $leaveType->default_days,
                    'used_quota' => 0,
                    'remaining_quota' => $leaveType->default_days,
                ]
            );
        }
    }

    /**
     * Handle the Employee "updated" event.
     */
    public function updated(Employee $employee): void
    {
        // Sync branch_id with linked user account
        if ($employee->user && $employee->wasChanged('branch_id')) {
            $employee->user->updateQuietly(['branch_id' => $employee->branch_id]);
        }
    }
}
