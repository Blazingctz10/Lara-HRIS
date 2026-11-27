<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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

        // 2. Auto-initialize Leave Balances for the current year (sesuai gender pegawai)
        $activeLeaveTypes = LeaveType::where('is_active', true)
            ->forEmployeeGender($employee->gender)
            ->get();
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
        if ($employee->user) {
            $userUpdates = [];

            if ($employee->wasChanged('branch_id')) {
                $userUpdates['branch_id'] = $employee->branch_id;
            }
            if ($employee->wasChanged('full_name') && filled($employee->full_name)) {
                $userUpdates['name'] = $employee->full_name;
            }
            if ($employee->wasChanged('email') && filled($employee->email)) {
                $userUpdates['email'] = $employee->email;
            }

            if ($userUpdates !== []) {
                $employee->user->updateQuietly($userUpdates);
            }
        }

        // Revoke active sessions immediately when an employee is marked Resigned or Terminated
        if (
            $employee->user_id
            && $employee->wasChanged('status')
            && in_array($employee->status, ['Resigned', 'Terminated'], true)
        ) {
            DB::table('sessions')->where('user_id', $employee->user_id)->delete();
        }
    }
}
