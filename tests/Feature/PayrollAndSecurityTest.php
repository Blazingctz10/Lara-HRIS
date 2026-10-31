<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Position;
use App\Models\User;
use App\Services\PayrollCalculationService;
use Tests\TestCase;

class PayrollAndSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    public function test_payroll_calculation_service_calculates_correctly(): void
    {
        $employee = Employee::first();
        $this->assertNotNull($employee);

        $service = app(PayrollCalculationService::class);
        $calc = $service->calculateForEmployee($employee, (int) date('m'), (int) date('Y'));

        $this->assertArrayHasKey('basic_salary', $calc);
        $this->assertArrayHasKey('net_salary', $calc);
        $this->assertArrayHasKey('bpjs_tk_deduction', $calc);
        $this->assertArrayHasKey('bpjs_kes_deduction', $calc);
        $this->assertArrayHasKey('pph21_deduction', $calc);
        $this->assertGreaterThan(0, $calc['basic_salary']);
        $this->assertGreaterThan(0, $calc['net_salary']);
    }

    public function test_batch_generate_payroll_creates_draft_slips(): void
    {
        $service = app(PayrollCalculationService::class);
        $month = 10;
        $year = 2026;

        $result = $service->generateBatchPayroll($month, $year);
        $this->assertTrue($result['success']);
        $this->assertGreaterThan(0, $result['generated']);

        $this->assertDatabaseHas('payrolls', [
            'period_month' => $month,
            'period_year' => $year,
            'status' => 'Draft',
        ]);
    }

    public function test_employee_can_only_see_their_own_paid_slip_in_portal(): void
    {
        // 1. Ambil 2 user dengan role 'employee'
        $userA = User::where('role', 'employee')->first();
        $userB = User::where('role', 'employee')->where('id', '!=', $userA->id)->first();
        
        $this->assertNotNull($userA);
        $this->assertNotNull($userB);

        $empA = $userA->employee;
        $empB = $userB->employee;

        // Buat slip gaji untuk A (Paid) dan B (Paid)
        $slipA = Payroll::create([
            'employee_id' => $empA->id,
            'period_month' => 10,
            'period_year' => 2026,
            'basic_salary' => 5000000,
            'net_salary' => 4800000,
            'status' => 'Paid',
            'payment_date' => now(),
        ]);

        $slipB = Payroll::create([
            'employee_id' => $empB->id,
            'period_month' => 10,
            'period_year' => 2026,
            'basic_salary' => 8000000,
            'net_salary' => 7500000,
            'status' => 'Paid',
            'payment_date' => now(),
        ]);

        // Login sebagai User A (Pegawai Biasa)
        $this->actingAs($userA);

        // Akses query AccessibleBy untuk Payroll
        $visibleSlips = Payroll::accessibleBy()->get();

        // Verifikasi User A HANYA melihat slip A miliknya sendiri
        $this->assertTrue($visibleSlips->contains('id', $slipA->id));
        $this->assertFalse($visibleSlips->contains('id', $slipB->id));
    }

    public function test_regular_employee_cannot_access_admin_payroll_resource(): void
    {
        $employeeUser = User::where('role', 'employee')->first();
        $this->assertNotNull($employeeUser);

        $this->actingAs($employeeUser);

        // Karyawan biasa tidak boleh bisa mengakses panel admin
        $this->assertFalse($employeeUser->canAccessPanel(\Filament\Facades\Filament::getPanel('admin')));
        $this->assertFalse(\App\Filament\Resources\Payrolls\PayrollResource::canViewAny());
    }

    public function test_hr_admin_can_login_and_access_admin_dashboard_and_payrolls(): void
    {
        $hrAdmin = User::where('email', 'hrd@bprbandaraya.com')->first();
        if (!$hrAdmin) {
            $hrAdmin = User::where('role', 'hr_admin')->first();
        }
        $this->assertNotNull($hrAdmin);

        $this->actingAs($hrAdmin);

        // HR Admin can access admin panel
        $this->assertTrue($hrAdmin->canAccessPanel(\Filament\Facades\Filament::getPanel('admin')));

        // HR Admin accessing /admin renders dashboard with AdminShortcutWidget without error
        $response = $this->get('/admin');
        $response->assertStatus(200);

        // HR Admin can access /admin/payrolls
        $responsePayrolls = $this->get('/admin/payrolls');
        $responsePayrolls->assertStatus(200);
    }
}
