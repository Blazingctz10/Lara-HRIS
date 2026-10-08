<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeavePrintController extends Controller
{
    public function printForm(LeaveRequest $leaveRequest)
    {
        $user = auth()->user();

        // Cek otorisasi akses
        if (! $user->isHrAdmin() && ! $user->isDireksi() && ! $user->isSuperAdmin()) {
            if ($user->employee && $user->employee->id !== $leaveRequest->employee_id) {
                abort(403, 'Anda tidak berhak mencetak formulir cuti pegawai lain.');
            }
        }

        $leaveRequest->load(['employee.branch', 'employee.position', 'employee.department', 'leaveType', 'approver', 'approverHrd', 'approverKomisaris']);
        $employee = $leaveRequest->employee;

        // Ambil data saldo cuti personalia
        $year = $leaveRequest->start_date ? $leaveRequest->start_date->year : now()->year;
        $balance = EmployeeLeaveBalance::where('employee_id', $employee->id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->where('year', $year)
            ->first();

        $initialQuota = $balance ? $balance->initial_quota : ($leaveRequest->leaveType->default_days ?? 12);
        $usedQuota = $balance ? $balance->used_quota : 0;
        $remainingQuota = $balance ? $balance->remaining_quota : $initialQuota;

        return view('leaves.print-form', compact(
            'leaveRequest',
            'employee',
            'balance',
            'initialQuota',
            'usedQuota',
            'remainingQuota',
            'year'
        ));
    }
}
