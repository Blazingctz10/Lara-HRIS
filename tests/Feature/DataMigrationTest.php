<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use App\Services\DataMigrationService;
use Tests\TestCase;

class DataMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    public function test_templates_can_be_generated(): void
    {
        $service = app(DataMigrationService::class);
        $empCsv = $service->generateEmployeeTemplateCsv();
        $attCsv = $service->generateAttendanceTemplateCsv();

        $this->assertStringContainsString('NIP,NIK,Nama_Lengkap', $empCsv);
        $this->assertStringContainsString('Hendri Prasetyo', $empCsv);
        $this->assertStringContainsString('NIP,Tanggal,Jam_Masuk', $attCsv);
    }

    public function test_employees_can_be_imported_from_csv(): void
    {
        $service = app(DataMigrationService::class);
        $csvContent = "NIP,NIK,Nama_Lengkap,Email,Nomor_Telepon,Jenis_Kelamin,Tanggal_Lahir,Alamat,Kantor_Cabang,Divisi,Jabatan,Status_Kerja,Tanggal_Masuk\n"
            . "TEST-001,2171010001,Karyawan Migrasi Satu,karyawan.satu@bprbandaraya.com,081233445566,Male,1992-05-10,Batam,Cabang Baru Tiban,Divisi Pemasaran Baru,Account Officer Senior,Permanent,2023-01-10\n";

        $tempFile = tempnam(sys_get_temp_dir(), 'csv_emp_');
        file_put_contents($tempFile, $csvContent);

        $result = $service->importEmployeesFromCsv($tempFile);
        @unlink($tempFile);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['imported']);

        $this->assertDatabaseHas('employees', [
            'nip' => 'TEST-001',
            'full_name' => 'Karyawan Migrasi Satu',
            'email' => 'karyawan.satu@bprbandaraya.com',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'karyawan.satu@bprbandaraya.com',
            'role' => 'employee',
        ]);

        $this->assertDatabaseHas('branches', [
            'name' => 'Cabang Baru Tiban',
        ]);

        $employee = Employee::where('nip', 'TEST-001')->first();
        $this->assertDatabaseHas('employee_leave_balances', [
            'employee_id' => $employee->id,
            'remaining_quota' => 12,
        ]);
    }

    public function test_attendances_can_be_imported_from_csv(): void
    {
        $employee = Employee::first();
        $this->assertNotNull($employee);

        $service = app(DataMigrationService::class);
        $csvContent = "NIP,Tanggal,Jam_Masuk,Jam_Keluar,Status,Catatan\n"
            . "{$employee->nip},2026-09-15,07:58:00,17:02:00,Present,Presensi Mesin Fingerprint\n";

        $tempFile = tempnam(sys_get_temp_dir(), 'csv_att_');
        file_put_contents($tempFile, $csvContent);

        $result = $service->importAttendancesFromCsv($tempFile);
        @unlink($tempFile);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['imported']);

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'date' => '2026-09-15',
            'status' => 'Present',
        ]);
    }

    public function test_admin_and_hr_can_access_import_page(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $this->actingAs($admin);

        $response = $this->get('/admin/import-data');
        $response->assertStatus(200);
        $response->assertSee('Pusat Migrasi & Impor Data Lama');
        $response->assertSee('Unduh Template CSV Pegawai');

        $hr = User::where('role', 'hr_admin')->first();
        $this->actingAs($hr);
        $response = $this->get('/admin/import-data');
        $response->assertStatus(200);
    }

    public function test_regular_employee_cannot_access_import_page(): void
    {
        $employee = User::where('email', 'teller.botania@bprbandaraya.com')->first();
        $this->actingAs($employee);

        $response = $this->get('/admin/import-data');
        $response->assertStatus(403);
    }
}
