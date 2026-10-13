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
        $response->assertSee('images/logo.png');
        $response->assertSee('Cuti Besar (6 HK)');
    }

    public function test_cuti_besar_is_configured_for_6_days(): void
    {
        $longLeave = LeaveType::where('code', 'LONG_LEAVE')->first();
        $this->assertNotNull($longLeave);
        $this->assertEquals(6, $longLeave->default_days);
        $this->assertEquals(6, $longLeave->max_consecutive_days);
    }

    public function test_direksi_has_read_only_access_to_employee_data(): void
    {
        // 1. Direksi bisa melihat daftar pegawai
        $responseIndex = $this->actingAs($this->direksiUser)
            ->get('/admin/employees');
        $responseIndex->assertStatus(200);

        // 2. Direksi bisa melihat halaman detail pegawai (View Record)
        $responseView = $this->actingAs($this->direksiUser)
            ->get("/admin/employees/{$this->employee->id}");
        $responseView->assertStatus(200);

        // 3. Direksi DILARANG mengakses halaman tambah pegawai (403 Forbidden)
        $responseCreate = $this->actingAs($this->direksiUser)
            ->get('/admin/employees/create');
        $responseCreate->assertStatus(403);

        // 4. Direksi DILARANG mengakses halaman edit pegawai (403 Forbidden)
        $responseEdit = $this->actingAs($this->direksiUser)
            ->get("/admin/employees/{$this->employee->id}/edit");
        $responseEdit->assertStatus(403);
    }

    public function test_attendance_import_and_export_merges_with_leave_and_dispensation(): void
    {
        $testDate = '2026-10-15';

        // 1. Employee 1 memiliki Cuti Resmi yang disetujui
        $leave = LeaveRequest::create([
            'employee_id' => $this->employee->id,
            'leave_type_id' => $this->annualLeave->id,
            'start_date' => $testDate,
            'end_date' => $testDate,
            'total_days' => 1,
            'reason' => 'Keperluan keluarga penting',
            'status' => 'Approved_Direksi',
        ]);

        // 2. Buat pegawai kedua dengan Dispensasi Logbook HRD/GA
        $emp2 = Employee::create([
            'user_id' => User::create([
                'name' => 'Staf Operasional 2',
                'email' => 'staf2@bprbandaraya.com',
                'password' => bcrypt('password'),
                'role' => 'employee',
            ])->id,
            'nip' => 'BNK-TEST-002',
            'full_name' => 'Staf Operasional 2',
            'email' => 'staf2@bprbandaraya.com',
            'branch_id' => $this->employee->branch_id,
            'status' => 'Active',
            'join_date' => '2023-01-01',
        ]);

        AttendanceDispensation::create([
            'employee_id' => $emp2->id,
            'date' => $testDate,
            'dispensation_type' => 'Sakit (Izin Chat WA/DM)',
            'custom_reason' => 'Demam mendadak izin via WA',
            'status' => 'Excused',
            'recorded_by_id' => $this->hrUser->id,
        ]);

        // 3. Simulasikan impor CSV log mesin presensi
        $csvContent = "nip,tanggal,jam_masuk,jam_keluar,status,catatan\n"
            . "{$this->employee->nip},{$testDate},08:15:00,17:00:00,Present,Tap Mesin Fingerprint\n"
            . "{$emp2->nip},{$testDate},,,Absent,Tidak Tap Mesin\n";

        $tempFile = tempnam(sys_get_temp_dir(), 'att_test_') . '.csv';
        file_put_contents($tempFile, $csvContent);

        $service = app(\App\Services\DataMigrationService::class);
        $result = $service->importAttendancesFromCsv($tempFile);
        $this->assertTrue($result['success']);

        unlink($tempFile);

        // 4. Verifikasi Employee 1 tetap tercatat sebagai Cuti Resmi (tidak kena penalti/terlambat)
        $att1 = Attendance::where('employee_id', $this->employee->id)->whereDate('date', $testDate)->first();
        $this->assertNotNull($att1);
        $this->assertEquals('Excused', $att1->status);
        $this->assertStringContainsString('Cuti Resmi', $att1->notes);
        $this->assertEquals(0, $att1->late_minutes);

        // 5. Verifikasi Employee 2 tetap tercatat sebagai Izin/Sakit Dispensasi (bukan Alpa)
        $att2 = Attendance::where('employee_id', $emp2->id)->whereDate('date', $testDate)->first();
        $this->assertNotNull($att2);
        $this->assertEquals('Excused', $att2->status);
        $this->assertStringContainsString('Dispensasi HRD/GA', $att2->notes);
    }

    public function test_late_arrival_dispensation_counts_as_present_and_waives_penalty(): void
    {
        $testDate = '2026-08-20';

        // 1. Employee mengajukan izin datang terlambat (misal karena ban bocor / kendala jalan)
        AttendanceDispensation::create([
            'employee_id' => $this->employee->id,
            'date' => $testDate,
            'dispensation_type' => 'Izin Telat Masuk',
            'custom_reason' => 'Izin telat masuk karena ban bocor di jalan',
            'recorded_by' => $this->hrUser->id,
        ]);

        // 2. Tarik / impor presensi dari mesin biometric (karyawan datang jam 08:35)
        $csvContent = "Name,Date,Timetable,On duty,Off duty,Clock In,Clock Out,OT Time\n"
            . "{$this->employee->full_name},20/08/2026,SENIN - KAMIS,08.00,16.00,08.35,16.15,\n";

        $tempFile = tempnam(sys_get_temp_dir(), 'att_late_') . '.csv';
        file_put_contents($tempFile, $csvContent);

        $service = app(\App\Services\DataMigrationService::class);
        $result = $service->importAttendancesFromFile($tempFile);
        $this->assertTrue($result['success']);
        unlink($tempFile);

        // 3. Verifikasi karyawan tetap tercatat sebagai Present (Hadir), bukan Excused atau Late denda
        $att = Attendance::where('employee_id', $this->employee->id)->whereDate('date', $testDate)->first();
        $this->assertNotNull($att);
        $this->assertEquals('Present', $att->status, 'Pegawai dengan izin telat masuk harus tetap terhitung Hadir.');
        $this->assertEquals(0, (float) $att->penalty_amount, 'Denda pegawai dengan izin telat masuk harus Rp0.');
        $this->assertEquals(35, $att->late_minutes, 'Menit keterlambatan tetap tercatat untuk histori.');
        $this->assertStringContainsString('Dispensasi Telat', $att->notes);
    }

    public function test_biometric_xls_template_imports_successfully(): void
    {
        $xlsPath = base_path('../Template/absen.xls');
        $this->assertFileExists($xlsPath);

        $service = app(\App\Services\DataMigrationService::class);
        $result = $service->importAttendancesFromFile($xlsPath);

        $this->assertTrue($result['success']);
        $this->assertGreaterThan(0, $result['imported']);
    }
}
