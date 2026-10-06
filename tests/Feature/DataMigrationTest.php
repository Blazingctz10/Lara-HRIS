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

    public function test_comprehensive_employee_profile_can_be_imported_from_csv(): void
    {
        $service = app(DataMigrationService::class);
        $csvContent = "NIP,NIK,Nama_Lengkap,Email_Kantor,Email_Pribadi,Nomor_Telepon,Jenis_Kelamin,Tempat_Lahir,Tanggal_Lahir,Alamat,Kantor_Cabang,Divisi,Jabatan,Tanggal_Efektif_Jabatan,Grade,Tanggal_Efektif_Grade,Status_Kerja,Tanggal_Masuk,Tanggal_Pengangkatan_Tetap,Akhir_Kontrak,Status_Pegawai,Jenjang_Pendidikan,Jurusan,Universitas,IPK,NPWP,No_BPJSTK,No_BPJS_Kes,Rekening_BPR,Nama_Bank_Lain,Nomor_Rekening_Bank_Lain,Atas_Nama_Rekening,Status_PTKP,Kontak_Darurat_Nama,Kontak_Darurat_Nomor,Kontak_Darurat_Hubungan\n"
            . "BPR-2026-9999,2171099999999999,Budi Santoso,budi.santoso@bprbandaraya.com,budi.pribadi@gmail.com,081299998888,Male,Batam,1990-01-01,Jl. Sudirman Batam,Kantor Cabang Botania,Operasional & Pelayanan,Customer Service,2024-01-01,5,2024-01-01,Permanent,2024-01-01,2025-01-01,,Active,S-1,Manajemen,UIB,3.75,81.999.888.7-215.000,08099999,00019999,01.10.99999,BCA,8210999999,Budi Santoso,K/1,Siti Rahayu,081399998888,Istri\n";

        $tempFile = tempnam(sys_get_temp_dir(), 'csv_comp_');
        file_put_contents($tempFile, $csvContent);

        $result = $service->importEmployeesFromCsv($tempFile);
        @unlink($tempFile);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['imported']);

        $this->assertDatabaseHas('employees', [
            'nip' => 'BPR-2026-9999',
            'grade' => 5,
            'education_level' => 'S-1',
            'education_institution' => 'UIB',
            'bank_external_name' => 'BCA',
            'ptkp_status' => 'K/1',
            'emergency_contact_name' => 'Siti Rahayu',
        ]);

        $importedEmployee = Employee::where('nip', 'BPR-2026-9999')->first();
        $this->assertNotNull($importedEmployee);
        $this->assertEquals('01.10.99999', $importedEmployee->bank_bpr_account);

        $this->assertDatabaseHas('employee_families', [
            'name' => 'Siti Rahayu',
            'relationship' => 'Istri',
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
