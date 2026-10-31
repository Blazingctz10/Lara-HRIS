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

    public function test_employees_can_be_imported_from_xlsx(): void
    {
        $service = app(DataMigrationService::class);
        $tempXlsx = tempnam(sys_get_temp_dir(), 'test_emp_') . '.xlsx';

        $zip = new \ZipArchive();
        $zip->open($tempXlsx, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $types = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/></Types>';
        $zip->addFromString('[Content_Types].xml', $types);

        $strings = ['NIP', 'NIK', 'Nama_Lengkap', 'Email', 'Nomor_Telepon', 'Kantor_Cabang', 'Jabatan', 'Status_Kerja', 'XLSX-001', '2171010099', 'Pegawai Dari XLSX', 'pegawai.xlsx@bprbandaraya.com', '081299990001', 'Kantor Cabang Botania', 'Customer Service', 'Permanent'];
        $sXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="'.count($strings).'">';
        foreach ($strings as $s) {
            $sXml .= '<si><t>' . htmlspecialchars($s) . '</t></si>';
        }
        $sXml .= '</sst>';
        $zip->addFromString('xl/sharedStrings.xml', $sXml);

        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData><row r="1"><c r="A1" t="s"><v>0</v></c><c r="B1" t="s"><v>1</v></c><c r="C1" t="s"><v>2</v></c><c r="D1" t="s"><v>3</v></c><c r="E1" t="s"><v>4</v></c><c r="F1" t="s"><v>5</v></c><c r="G1" t="s"><v>6</v></c><c r="H1" t="s"><v>7</v></c></row><row r="2"><c r="A2" t="s"><v>8</v></c><c r="B2" t="s"><v>9</v></c><c r="C2" t="s"><v>10</v></c><c r="D2" t="s"><v>11</v></c><c r="E2" t="s"><v>12</v></c><c r="F2" t="s"><v>13</v></c><c r="G2" t="s"><v>14</v></c><c r="H2" t="s"><v>15</v></c></row></sheetData></worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->close();

        $result = $service->importEmployeesFromCsv($tempXlsx);
        @unlink($tempXlsx);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['imported']);

        $this->assertDatabaseHas('employees', [
            'nip' => 'XLSX-001',
            'full_name' => 'Pegawai Dari XLSX',
            'email' => 'pegawai.xlsx@bprbandaraya.com',
        ]);
    }

    public function test_employees_can_be_imported_from_pdf(): void
    {
        $service = app(DataMigrationService::class);
        $tempPdf = tempnam(sys_get_temp_dir(), 'test_emp_') . '.pdf';

        $mockPdf = "%PDF-1.4\n1 0 obj\n<< /Length 300 >>\nstream\nBT\n/F1 12 Tf\n(NIP | NIK | Nama_Lengkap | Email | Kantor_Cabang | Jabatan | Status_Kerja) Tj\nET\nBT\n(PDF-001 | 2171018888 | Pegawai Dari PDF | pegawai.pdf@bprbandaraya.com | Kantor Cabang Botania | Teller | Permanent) Tj\nET\nendstream\nendobj\nxref\n0 2\ntrailer\n<< /Root 1 0 R >>\n%%EOF";
        file_put_contents($tempPdf, $mockPdf);

        $result = $service->importEmployeesFromCsv($tempPdf);
        @unlink($tempPdf);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['imported']);

        $this->assertDatabaseHas('employees', [
            'nip' => 'PDF-001',
            'full_name' => 'Pegawai Dari PDF',
            'email' => 'pegawai.pdf@bprbandaraya.com',
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

    public function test_employees_can_be_imported_from_official_bpr_cv_format(): void
    {
        $service = app(DataMigrationService::class);
        $tempCsv = tempnam(sys_get_temp_dir(), 'bpr_cv_') . '.csv';

        $bprCsvContent = <<<'CSV'
I. DATA PRIBADI;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
Nama;;;;;;:;RIA ARDILA TEST;;;;;;;;;29-Okt-26;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
NIK;;;;;;:;1204 9999;;;;;;;;;;;;;;;;;;;;;;;;;;Kantor Pusat;;;;;;;;;;;;;;;;;;;;;;
Jabatan;;;;;;:;Manager Operational;;;;;;;;;;;;;;;;;;;;;;;;;;KC Botania;;;;;;;;;;;;;;;;;;;;;;
Unit Kerja;;;;;;:;Kantor Pusat;;;;;;;;;;;;;;;;;;;;;;;;;;KC Aviari;;;;;;;;;;;;;;;;;;;;;;
Tgl Efektif Jabatan;;;;;;:;22-Nov-21;;;;;;;;;;;;;;;;;;;;;;;;;;K. Kas Bengkong;;;;;;;;;;;;;;;;;;;;;;
Grade;;;;;;:;6;;;;;;;;;;;;;;;;;;;;;;Aktif;;;;;;;;;;;;;;;;;;;;;;;;;;
Tgl Efektif Grade;;;;;;:;01-Jan-20;;;;;;;;;;;;;;;;;;;;;;Keluar;;;;;;;;;;;;;;;;;;;;;;;;;;
Join Date;;;;;;:;09-Jan-12;;;;;;;;;;;;;;;;;;;;;;;;Laki-laki;;;;;;;;;;;;;;;;;;;;;;;;
Tgl Permanent;;;;;;:;09-Apr-14;;;;;;;;;;;;;;;;;;;;;;;;Perempuan;;;;;;;;;;;;;;;;;;;;;;;;
Aktif / Keluar;;;;;;:;Keluar;;;Tgl Keluar;;;:;31-Agu-23;;;;Keterangan ;;;:;Mengundurkan diri;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
Tempat Lahir;;;;;;:;Parakan;;;;;;;;;;;;;;;;;;;;;;;;;Diberhentikan;;;;;;;;;;;;;;;;;;;;;;;
Tgl Lahir;;;;;;:;17-Jan-90;;;;;;;;;;;;;;;;;;;;;;;;;Usia Pensiun;;;;;;;;;;;;;;;;;;;;;;;
L/P;;;;;;:;Perempuan;;;;;;;;;;;;;;;;;;;;;;;SMP;;;;TK/0;;;;;;;;;;;;;;;;;;;;;
Jenjang Pendidikan;;;;;;:;S-1;;;;;;;;;;;;;;;;;;;;;;;SMK;;;;TK/2;;;;;;;;;;;;;;;;;;;;;
Jurusan;;;;;;:;Akuntansi;;;;;;;;;;;;;;;;;;;;;;;D-1;;;;TK/3;;;;;;;;;;;;;;;;;;;;;
Univ;;;;;;:;Univ. Batam;;;;;;;;;;;;;;;;;;;;;;;D-3;;;;K/0;;;;;;;;;;;;;;;;;;;;;
IPK;;;;;;:;2,98;;;;;;;;;;;;;;;;;;;;;;;D-4;;;;K/1;;;;;;;;;;;;;;;;;;;;;
Alamat;;;;;;:;Perum. Marbella Residence Blok C5 No. 01;;;;;;;;;;;;;;;;;;;;;;;S-2;;;;K/3;;;;;;;;;;;;;;;;;;;;;
No. HP;;;;;;:;0822 8383 0547;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
Email;;;;;;:;riaardilla2016@gmail.com;;;;;;;ria.test@bprbandaraya.com;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
No. KTP;;;;;;:;2171045701909999;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
No. NPWP;;;;;;:;69.519.009.0-215.000;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
No. BPJSTK;;;;;;:;08011529719;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
No. BPJS Kes;;;;;;:;0001289893544;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
Rek. BR;;;;;;:;01.10.05857;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
Rek. Mandiri;;;;;;:;109-00-1514589-9;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
II. DATA KELUARGA;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
Status PTKP;;;;;;:;TK/0;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
Emergency Call;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
Nama;;;;;;:;Ade Rahmat;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
No.HP;;;;;;:;081299991111;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
Hubungan;;;;;;:;Suami;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
III. RIWAYAT KARIR;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
No;;Unit Kerja;;;;;;;Posisi;;;;;;;;;Tgl Efektif Jabatan;;;;No. Surat Pengangkatan;;;;;;;;;Grade;;;Tgl Efektif Grade;;;;Keterangan;;;;;;;;;;;;;;;;;
1;;KC Botania;;;;;;;Service Assistant;;;;;;;;;09-Apr-12;;;;005/KK-HRD/BPRBR/IV/2012;;;;;;;;;2;;;09-Apr-12;;;;Karyawan Kontrak;;;;;;;;;;;;;;;;;
IV. RIWAYAT PELATIHAN;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;V. PERFORMANCE APPRAISAL (PA);;;
No;;Nama Pelatihan;;;;;;;;;;;;;;;;Jadwal;;;;Penyelenggara;;;;;;;;;Pemateri;;;;;;;Lokasi;;;;;;;;;;;;;;No;Periode Penilaian (Tahun Buku);Nilai;Angka Mutu
1;;Aspek Legalitas Dalam Perkreditan;;;;;;;;;;;;;;;;22-Des-12;;;;BPR Banda Raya;;;;;;;;;LBKH An-Nisa;;;;;;;Nongsa Point Marina;;;;;;;;;;;;;;1;2012;B; 3,73 
CSV;

        file_put_contents($tempCsv, $bprCsvContent);
        $result = $service->importEmployeesFromCsv($tempCsv);
        @unlink($tempCsv);

        $this->assertTrue($result['success']);
        $this->assertGreaterThanOrEqual(1, $result['imported']);

        $emp = Employee::where('full_name', 'RIA ARDILA TEST')->first();
        $this->assertNotNull($emp);
        $this->assertEquals('1204 9999', $emp->nip);
        $this->assertEquals('ria.test@bprbandaraya.com', $emp->email);
        $this->assertEquals('riaardilla2016@gmail.com', $emp->personal_email);
        $this->assertEquals('2171045701909999', $emp->nik);
        $this->assertEquals('Resigned', $emp->status);
        $this->assertEquals('Mengundurkan diri', $emp->exit_reason);
        $this->assertEquals('2023-08-31', $emp->exit_date->toDateString());
        $this->assertEquals(6, $emp->grade);
        $this->assertEquals('S-1', $emp->education_level);
        $this->assertEquals('Akuntansi', $emp->education_major);
        $this->assertEquals('Univ. Batam', $emp->education_institution);
        $this->assertEquals(2.98, (float) $emp->ipk);
        $this->assertEquals('Bank Mandiri', $emp->bank_external_name);
        $this->assertEquals('109-00-1514589-9', $emp->bank_external_account);
        $this->assertEquals('TK/0', $emp->ptkp_status);

        // Cek Career Movement
        $this->assertDatabaseHas('career_movements', [
            'employee_id' => $emp->id,
            'sk_number' => '005/KK-HRD/BPRBR/IV/2012',
        ]);

        // Cek Pelatihan
        $this->assertDatabaseHas('employee_trainings', [
            'employee_id' => $emp->id,
            'training_name' => 'Aspek Legalitas Dalam Perkreditan',
        ]);

        // Cek Performance Appraisal
        $this->assertDatabaseHas('employee_performance_appraisals', [
            'employee_id' => $emp->id,
            'period_year' => 2012,
            'letter_grade' => 'B',
        ]);
    }

    public function test_director_and_executive_position_resolution_uses_valid_enum_grade_level(): void
    {
        $service = app(DataMigrationService::class);
        $dept = \App\Models\Department::firstOrCreate(['name' => 'Direksi'], ['code' => 'DIR01']);

        // Test position with Direktur / Executive
        $pos = $service->resolveBprPosition('Direktur Kepatuhan', $dept);
        $this->assertEquals('Direktur Kepatuhan', $pos->name);
        $this->assertEquals('Division Head', $pos->grade_level);

        // Test position with Manager
        $posMgr = $service->resolveBprPosition('Manager Operasional', $dept);
        $this->assertEquals('Manager', $posMgr->grade_level);

        // Test position with Supervisor
        $posSpv = $service->resolveBprPosition('Supervisor Kredit', $dept);
        $this->assertEquals('Supervisor', $posSpv->grade_level);

        // Test position with Officer
        $posOfficer = $service->resolveBprPosition('Account Officer Funding', $dept);
        $this->assertEquals('Officer', $posOfficer->grade_level);

        // Verify all grade levels are in allowed SQLite enum list
        $allowed = ['Staff', 'Officer', 'Supervisor', 'Assistant Manager', 'Manager', 'Vice President', 'Division Head'];
        $this->assertTrue(in_array($pos->grade_level, $allowed, true));
    }
}

