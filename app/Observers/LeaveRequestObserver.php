<?php

namespace App\Observers;

use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeaveRequestObserver
{
    /**
     * Handle the LeaveRequest "updated" event.
     */
    public function updated(LeaveRequest $leaveRequest): void
    {
        if ($leaveRequest->wasChanged('status')) {
            $originalStatus = $leaveRequest->getOriginal('status');
            $newStatus = $leaveRequest->status;
            $year = Carbon::parse($leaveRequest->start_date)->year;

            $isFinalApproved = in_array($newStatus, ['Approved_Direksi', 'Approved_HR']);
            $wasFinalApproved = in_array($originalStatus, ['Approved_Direksi', 'Approved_HR']);

            // Ketika disetujui final oleh Direksi (atau HR), potong saldo cuti resmi
            if ($isFinalApproved && ! $wasFinalApproved) {
                DB::transaction(function () use ($leaveRequest, $year) {
                    $balance = EmployeeLeaveBalance::firstOrCreate(
                        [
                            'employee_id' => $leaveRequest->employee_id,
                            'leave_type_id' => $leaveRequest->leave_type_id,
                            'year' => $year,
                        ],
                        [
                            'initial_quota' => $leaveRequest->leaveType->default_days ?? 12,
                            'used_quota' => 0,
                            'remaining_quota' => $leaveRequest->leaveType->default_days ?? 12,
                        ]
                    );

                    $balance->used_quota += $leaveRequest->total_days;
                    $balance->remaining_quota = max(0, $balance->initial_quota - $balance->used_quota);
                    $balance->save();

                    Log::info("Leave balance deducted for employee {$leaveRequest->employee_id}: {$leaveRequest->total_days} days. Remaining: {$balance->remaining_quota}");
                });
            }

            // Jika status yang sudah disetujui direksi dibatalkan/ditolak, kembalikan kuota saldo cuti
            if ($wasFinalApproved && ! $isFinalApproved) {
                DB::transaction(function () use ($leaveRequest, $year) {
                    $balance = EmployeeLeaveBalance::where([
                        'employee_id' => $leaveRequest->employee_id,
                        'leave_type_id' => $leaveRequest->leave_type_id,
                        'year' => $year,
                    ])->first();

                    if ($balance) {
                        $balance->used_quota = max(0, $balance->used_quota - $leaveRequest->total_days);
                        $balance->remaining_quota = min($balance->initial_quota, $balance->initial_quota - $balance->used_quota);
                        $balance->save();
                    }
                });
            }
        }
    }
}
