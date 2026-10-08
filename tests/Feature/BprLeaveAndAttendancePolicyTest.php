<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceDispensation;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BprLeaveAndAttendancePolicyTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected User $hrUser;
    protected User $direksiUser;
    protected User $komisarisUser;
    protected User $employeeUser;
    protected Employee $employee;
    protected Employee $direksiEmployee;
    protected LeaveType $annualLeave;
    protected LeaveType $paternityLeave;

    protected function setUp(): void
    {
        parent::setUp();

        $branch = Branch::where('code', 'HO')->orWhere('code', 'KC-BOTANIA')->first();
        $dept = Department::first();
        $pos = Position::first();

        $this->hrUser = User::where('role', 'hr_admin')->first() ?? User::create([
            'name' => 'HR Manager',
            'email' => 'hr_test@bprbandaraya.com',
            'password' => bcrypt('password'),
            'role' => 'hr_admin',
            'branch_id' => $branch->id,
        ]);

        $this->direksiUser = User::where('role', 'direksi')->first() ?? User::create([
            'name' => 'Direktur BPR',
            'email' => 'direksi_test@bprbandaraya.com',
            'password' => bcrypt('password'),
            'role' => 'direksi',
            'branch_id' => $branch->id,
        ]);

        $this->komisarisUser = User::where('role', 'komisaris')->first() ?? User::create([
            'name' => 'Komisaris Utama',
            'email' => 'komisaris@bprbandaraya.com',
            'password' => bcrypt('password'),
            'role' => 'komisaris',
            'branch_id' => $branch->id,
        ]);

        $this->employeeUser = User::where('role', 'employee')->first() ?? User::create([
            'name' => 'Budi Staf Operasional',
            'email' => 'budi_test@bprbandaraya.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
            'branch_id' => $branch->id,
        ]);

        $this->employee = $this->employeeUser->employee ?? Employee::create([
            'user_id' => $this->employeeUser->id,
            'branch_id' => $branch->id,
            'department_id' => $dept->id,
            'position_id' => $pos->id,
            'nip' => 'BPR-2026-9999',
            'nik' => '2171010101909999',
            'full_name' => 'Budi Santoso',
            'gender' => 'Laki-laki',
            'status' => 'Active',
        ]);

        $this->direksiEmployee = $this->direksiUser->employee ?? Employee::create([
            'user_id' => $this->direksiUser->id,
            'branch_id' => $branch->id,
            'department_id' => $dept->id,
            'position_id' => $pos->id,
            'nip' => 'BPR-DIR-9999',
            'nik' => '2171010101709999',
            'full_name' => 'Bapak Direktur',
            'gender' => 'Laki-laki',
            'status' => 'Active',
        ]);

        $this->annualLeave = LeaveType::where('code', 'ANNUAL')->first();
        $this->paternityLeave = LeaveType::where('code', 'PATERNITY')->first();

        EmployeeLeaveBalance::firstOrCreate(
            [
                'employee_id' => $this->employee->id,
                'leave_type_id' => $this->annualLeave->id,
                'year' => now()->year,
            ],
            [
                'initial_quota' => 12,
                'used_quota' => 0,
                'remaining_quota' => 12,
            ]
        );
    }

    public function test_late_clock_in_incurs_50k_penalty(): void
    {
        $testDateLate = '2026-11-16';

        // 1. Masuk jam 08:14:00 (Terlambat 14 menit)
        $attLate = Attendance::updateOrCreate(
            ['employee_id' => $this->employee->id, 'date' => $testDateLate],
            ['clock_in' => '08:14:00', 'status' => 'Present']
        );

        $this->assertEquals('Late', $attLate->status);
        $this->assertEquals(14, $attLate->late_minutes);
        $this->assertEquals(50000.00, (float) $attLate->penalty_amount);

        // 2. Masuk tepat waktu jam 07:55:00 (Tepat waktu)
        $testDateOnTime = '2026-11-17';
        $attOnTime = Attendance::updateOrCreate(
            ['employee_id' => $this->employee->id, 'date' => $testDateOnTime],
            ['clock_in' => '07:55:00', 'status' => 'Present']
        );

        $this->assertEquals('Present', $attOnTime->status);
        $this->assertEquals(0, $attOnTime->late_minutes);
        $this->assertEquals(0.00, (float) $attOnTime->penalty_amount);
    }

    public function test_attendance_dispensation_logbook_auto_syncs_to_excused(): void
    {
        $dispDate = '2026-11-18';

        // HRD/GA mencatat izin by WhatsApp dengan opsi isi sendiri
        $disp = AttendanceDispensation::create([
            'employee_id' => $this->employee->id,
            'date' => $dispDate,
            'dispensation_type' => 'Lainnya / Isi Sendiri',
            'custom_reason' => 'Izin ban motor bocor via WA',
            'description' => 'Konfirmasi via WA ke Pak HR jam 07:40',
            'recorded_by' => $this->hrUser->id,
        ]);

        // Verifikasi otomatis tercatat di tabel attendances
        $att = Attendance::where('employee_id', $this->employee->id)
            ->where('date', $dispDate)
            ->first();

        $this->assertNotNull($att);
        $this->assertEquals('Excused', $att->status);
        $this->assertStringContainsString('Izin ban motor bocor via WA', $att->notes);
    }

    public function test_direksi_short_leave_approved_by_hrd(): void
    {
        // Cuti 2 hari untuk Direksi (<= 2 hari)
        $leave = LeaveRequest::create([
            'employee_id' => $this->direksiEmployee->id,
            'leave_type_id' => $this->annualLeave->id,
            'start_date' => '2026-11-23',
            'end_date' => '2026-11-24',
            'total_days' => 2,
            'reason' => 'Keperluan keluarga mendesak',
            'status' => 'Pending',
        ]);

        $this->assertTrue($leave->isDireksiRequest());
        $this->assertFalse($leave->requiresKomisarisApproval()); // <= 2 hari tidak butuh komisaris

        // HRD menyetujui
        $leave->update([
            'status' => 'Approved_HRD',
            'approved_hrd_by' => $this->hrUser->id,
        ]);

        $this->assertEquals('Approved_HRD', $leave->fresh()->status);
    }

    public function test_direksi_long_leave_requires_komisaris_approval(): void
    {
        // Cuti 4 hari untuk Direksi (> 2 hari)
        $leave = LeaveRequest::create([
            'employee_id' => $this->direksiEmployee->id,
            'leave_type_id' => $this->annualLeave->id,
            'start_date' => '2026-11-23',
            'end_date' => '2026-11-26',
            'total_days' => 4,
            'reason' => 'Cuti panjang akhir tahun',
            'status' => 'Pending',
        ]);

        $this->assertTrue($leave->isDireksiRequest());
        $this->assertTrue($leave->requiresKomisarisApproval()); // > 2 hari butuh komisaris

        // Tahap 1: HRD meneruskan ke Dewan Komisaris
        $leave->update([
            'status' => 'Pending_Komisaris',
            'approved_hrd_by' => $this->hrUser->id,
        ]);

        // Tahap 2: Dewan Komisaris menyetujui
        $leave->update([
            'status' => 'Approved_Komisaris',
            'approved_komisaris_by' => $this->komisarisUser->id,
            'komisaris_notes' => 'Disetujui dalam rapat Dewan Komisaris No. 012/KOM-BR/2026',
        ]);

        $this->assertEquals('Approved_Komisaris', $leave->fresh()->status);
    }

    public function test_approved_leave_auto_syncs_to_attendance(): void
    {
        $startDate = '2026-11-25';
        $endDate = '2026-11-26';

        $leave = LeaveRequest::create([
            'employee_id' => $this->employee->id,
            'leave_type_id' => $this->annualLeave->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => 2,
            'reason' => 'Acara keluarga',
            'status' => 'Pending',
        ]);

        // Direksi approve final (transisi dari Pending ke Approved_Direksi)
        $leave->update([
            'status' => 'Approved_Direksi',
            'approved_by' => $this->direksiUser->id,
        ]);

        // Verifikasi saldo terpotong 2 hari
        $balance = EmployeeLeaveBalance::where('employee_id', $this->employee->id)
            ->where('leave_type_id', $this->annualLeave->id)
            ->first();
        $this->assertEquals(2, $balance->used_quota);
        $this->assertEquals(10, $balance->remaining_quota);

        // Verifikasi tanggal cuti otomatis berstatus Excused di attendances
        $attDay1 = Attendance::where('employee_id', $this->employee->id)
            ->where('date', $startDate)
            ->first();
        $this->assertNotNull($attDay1);
        $this->assertEquals('Excused', $attDay1->status);
        $this->assertStringContainsString('Cuti Tahunan', $attDay1->notes);
    }

    public function test_printable_leave_form_renders_official_bpr_layout(): void
    {
        $leave = LeaveRequest::create([
            'employee_id' => $this->employee->id,
            'leave_type_id' => $this->annualLeave->id,
            'start_date' => '2026-11-23',
            'end_date' => '2026-11-25',
            'total_days' => 3,
            'reason' => 'Mudik lebaran ke kampung halaman',
            'leave_address' => 'Jl. Merdeka No. 45, Batam',
            'contact_phone' => '0812-9988-7766',
            'status' => 'Approved_Direksi',
        ]);

        $response = $this->actingAs($this->hrUser)
            ->get(route('leave-requests.print', $leave));

        $response->assertStatus(200);
        $response->assertSee('BANK PEREKONOMIAN RAKYAT BANDA RAYA');
        $response->assertSee('FORMULIR PERMOHONAN CUTI');
        $response->assertSee(strtoupper($this->employee->full_name));
        $response->assertSee('Mudik lebaran ke kampung halaman');
        $response->assertSee('0812-9988-7766');
        $response->assertSeeText('HRD & GA', false);
        $response->assertSee('Direktur Utama');
    }
}
