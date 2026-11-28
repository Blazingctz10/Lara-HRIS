<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Payroll;
use App\Models\Position;
use App\Models\User;
use App\Services\GeofencingService;
use App\Services\PayrollExcelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollExcelAndDivisionLeaveTest extends TestCase
{
    use RefreshDatabase;

    public function test_reimbursement_module_is_removed_and_geofencing_is_disabled_by_default(): void
    {
        $this->assertFileDoesNotExist(app_path('Models/Reimbursement.php'));
        $this->assertDirectoryDoesNotExist(app_path('Filament/Resources/Reimbursements'));
        $this->assertDirectoryDoesNotExist(app_path('Filament/Portal/Resources/Reimbursements'));
        $this->assertFalse(GeofencingService::isEnabled());
    }

    public function test_payroll_excel_import_stores_exact_values_and_distributes_slips(): void
    {
        $branch = Branch::create([
            'code' => 'HO',
            'name' => 'Kantor Pusat',
            'city' => 'Batam',
            'address' => 'Batam',
            'is_active' => true,
        ]);
        $position = Position::create([
            'name' => 'Staff Operasional',
            'grade' => 4,
            'base_salary_min' => 4500000,
            'base_salary_max' => 6000000,
        ]);
        $employee = Employee::create([
            'nip' => '25108888',
            'full_name' => 'Rina Payroll Excel',
            'email' => 'rina@bprbandaraya.com',
            'branch_id' => $branch->id,
            'position_id' => $position->id,
            'status' => 'Active',
            'join_date' => now()->toDateString(),
        ]);

        $csvPath = tempnam(sys_get_temp_dir(), 'payroll_test_') . '.csv';
        file_put_contents($csvPath, implode("\n", [
            'NIP,Nama Pegawai,Unit Kantor,Gaji Pokok,Tunjangan Jabatan,Tunjangan Transport,Uang Makan,Uang Lembur,Potongan BPJS TK,Potongan BPJS Kes,Potongan PPh 21,Potongan Denda Telat,Potongan Kasbon,Gaji Bersih (THP),Catatan',
            '25108888,Rina Payroll Excel,Kantor Pusat,5000000,500000,400000,500000,200000,150000,50000,100000,0,250000,6050000,Gaji November dari Excel HR',
        ]));

        $service = app(PayrollExcelService::class);
        $result = $service->importFromExcel($csvPath, 11, 2026, false);
        @unlink($csvPath);

        $this->assertTrue($result['success']);
        $this->assertSame(1, $result['imported']);

        $payroll = Payroll::where('employee_id', $employee->id)->first();
        $this->assertNotNull($payroll);
        $this->assertSame('Draft', $payroll->status);
        $this->assertSame(5000000.0, (float) $payroll->basic_salary);
        $this->assertSame(6050000.0, (float) $payroll->net_salary);

        $released = $service->releasePeriod(11, 2026);
        $this->assertSame(1, $released);
        $this->assertSame('Paid', $payroll->fresh()->status);
    }

    public function test_division_leave_workflow_supports_free_text_and_leader_to_direksi_acc(): void
    {
        $ho = Branch::create(['code' => 'HO', 'name' => 'Kantor Pusat', 'city' => 'Batam', 'address' => 'Batam', 'is_active' => true]);
        $cabang = Branch::create(['code' => 'KC1', 'name' => 'KC Botania', 'city' => 'Batam', 'address' => 'Botania', 'is_active' => true]);

        $deptOps = Department::create(['code' => 'OPS01', 'name' => 'Operasional & Layanan Kas']);
        $deptBisnis = Department::create(['code' => 'BKP01', 'name' => 'Bisnis, Kredit & Pemasaran']);

        $posOps = Position::create(['department_id' => $deptOps->id, 'name' => 'Teller', 'grade' => 3]);
        $posSupport = Position::create(['department_id' => $deptBisnis->id, 'name' => 'Admin Kredit Support', 'grade' => 4]);
        $posBisnis = Position::create(['department_id' => $deptBisnis->id, 'name' => 'Manager Bisnis', 'grade' => 7]);

        $empOps = Employee::create(['nip' => '1001', 'full_name' => 'Ops Staff', 'email' => 'ops@bprbandaraya.com', 'branch_id' => $ho->id, 'department_id' => $deptOps->id, 'position_id' => $posOps->id, 'status' => 'Active', 'join_date' => now()->toDateString()]);
        $empSupport = Employee::create(['nip' => '1002', 'full_name' => 'Support Staff', 'email' => 'sup@bprbandaraya.com', 'branch_id' => $ho->id, 'department_id' => $deptBisnis->id, 'position_id' => $posSupport->id, 'status' => 'Active', 'join_date' => now()->toDateString()]);
        $empBisnis = Employee::create(['nip' => '1003', 'full_name' => 'Bisnis Staff', 'email' => 'bis@bprbandaraya.com', 'branch_id' => $ho->id, 'department_id' => $deptBisnis->id, 'position_id' => $posBisnis->id, 'status' => 'Active', 'join_date' => now()->toDateString()]);
        $empCabang = Employee::create(['nip' => '1004', 'full_name' => 'Cabang Staff', 'email' => 'cab@bprbandaraya.com', 'branch_id' => $cabang->id, 'department_id' => $deptOps->id, 'position_id' => $posOps->id, 'status' => 'Active', 'join_date' => now()->toDateString()]);

        $this->assertSame('operasional', LeaveRequest::resolveDivisionCategory($empOps));
        $this->assertSame('kredit_support', LeaveRequest::resolveDivisionCategory($empSupport));
        $this->assertSame('bisnis', LeaveRequest::resolveDivisionCategory($empBisnis));
        $this->assertSame('cabang', LeaveRequest::resolveDivisionCategory($empCabang));

        $leaveType = LeaveType::create(['code' => 'ANNUAL', 'name' => 'Cuti Tahunan', 'default_days' => 12, 'is_active' => true]);
        $leader = User::factory()->create(['role' => 'division_leader', 'division_scope' => 'operasional', 'email' => 'leader.ops@bprbandaraya.com']);
        $direksi = User::factory()->create(['role' => 'direksi', 'email' => 'direksi@bprbandaraya.com']);

        $leave = LeaveRequest::create([
            'employee_id' => $empOps->id,
            'leave_type_id' => $leaveType->id,
            'leave_type_custom' => 'Keperluan Keluarga Tanpa Batas Kuota',
            'division_category' => 'operasional',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(20)->toDateString(),
            'total_days' => 20,
            'reason' => 'Acara keluarga besar di luar kota',
            'status' => 'Pending',
        ]);

        $this->assertSame('Keperluan Keluarga Tanpa Batas Kuota', $leave->leave_type_display);

        $leave->update([
            'status' => 'Approved_Leader',
            'approved_leader_by' => $leader->id,
            'leader_signed_at' => now(),
            'leader_signature_name' => $leader->name,
        ]);

        $leave->update([
            'status' => 'Approved_Direksi',
            'approved_by' => $direksi->id,
            'direksi_signed_at' => now(),
            'direksi_signature_name' => $direksi->name,
            'acc_at' => now(),
        ]);

        $this->assertSame('Approved_Direksi', $leave->fresh()->status);
        $this->assertNotNull($leave->fresh()->acc_at);
    }
}
