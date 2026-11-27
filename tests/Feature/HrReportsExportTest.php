<?php

namespace Tests\Feature;

use App\Filament\Resources\LeaveRequests\Pages\ListLeaveRequests;
use App\Filament\Resources\Overtimes\Pages\ListOvertimes;
use App\Filament\Resources\Payrolls\Pages\ListPayrolls;
use App\Models\Employee;
use App\Models\Overtime;
use App\Models\Payroll;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class HrReportsExportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    public function test_hr_admin_can_export_payroll_leave_balance_and_overtime_excel_reports(): void
    {
        $hrAdmin = User::where('role', 'hr_admin')->firstOrFail();
        $employee = Employee::firstOrFail();

        Payroll::create([
            'employee_id' => $employee->id,
            'period_month' => (int) date('m'),
            'period_year' => (int) date('Y'),
            'basic_salary' => 5000000,
            'net_salary' => 4850000,
            'status' => 'Paid',
            'payment_date' => now(),
        ]);

        Overtime::create([
            'employee_id' => $employee->id,
            'date' => today()->toDateString(),
            'start_time' => '17:30:00',
            'end_time' => '19:30:00',
            'duration_hours' => 2,
            'reason' => 'Tutup buku akhir bulan',
            'status' => 'Approved_HR',
        ]);

        $this->actingAs($hrAdmin);

        Livewire::test(ListPayrolls::class)
            ->callAction('export_payroll_excel', [
                'month' => (int) date('m'),
                'year' => (int) date('Y'),
                'status' => 'all',
            ])
            ->assertFileDownloaded(sprintf('Rekap_Payroll_BPR_%04d_%02d.xlsx', (int) date('Y'), (int) date('m')));

        Livewire::test(ListLeaveRequests::class)
            ->callAction('export_leave_balances', [
                'year' => (int) date('Y'),
                'gender' => 'all',
                'status' => 'Active',
            ])
            ->assertFileDownloaded('Rekap_Saldo_Cuti_BPR_' . date('Y') . '_Semua.xlsx');

        Livewire::test(ListLeaveRequests::class)
            ->callAction('export_leave_balances', [
                'year' => (int) date('Y'),
                'gender' => 'Female',
                'status' => 'Active',
            ])
            ->assertFileDownloaded('Rekap_Saldo_Cuti_BPR_' . date('Y') . '_Perempuan.xlsx');

        Livewire::test(ListOvertimes::class)
            ->callAction('export_overtime_excel', [
                'month' => (int) date('m'),
                'year' => (int) date('Y'),
            ])
            ->assertFileDownloaded(sprintf('Rekap_Lembur_BPR_%04d_%02d.xlsx', (int) date('Y'), (int) date('m')));
    }
}
