<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveType;
use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = Hash::make('password');

        // =========================================================================
        // 1. DAFTAR KANTOR (Pusat, Cabang, dan Kas - Wilayah Batam)
        // =========================================================================
        $ho = Branch::updateOrCreate(
            ['code' => 'HO-001'],
            [
                'name' => 'Kantor Pusat BPR Banda Raya',
                'type' => 'Pusat',
                'city' => 'Batam Kota',
                'address' => 'Jl. Duyung, Komp. Marina Centre No 8-9, Sei Jodoh, Batam',
                'phone' => '(0778) 424100',
                'latitude' => 1.15120000,
                'longitude' => 104.01560000,
                'radius_meters' => 100,
                'is_active' => true,
            ]
        );

        $kcBotania = Branch::updateOrCreate(
            ['code' => 'KC-BOTANIA'],
            [
                'name' => 'Kantor Cabang Botania',
                'type' => 'Cabang',
                'city' => 'Batam Centre',
                'address' => 'Komp. Ruko Botania Garden Blok B2 No. 1-2, Batam Centre, Batam',
                'phone' => '(0778) 7495 400',
                'latitude' => 1.11650000,
                'longitude' => 104.08900000,
                'radius_meters' => 50,
                'is_active' => true,
            ]
        );

        $kcAviari = Branch::updateOrCreate(
            ['code' => 'KC-AVIARI'],
            [
                'name' => 'Kantor Cabang Aviari',
                'type' => 'Cabang',
                'city' => 'Batu Aji',
                'address' => 'Komp. Pertokoan Aviari Pratama Blok B No. 12-14, Batu Aji, Batam',
                'phone' => '(0778) 361100',
                'latitude' => 1.05050000,
                'longitude' => 103.97800000,
                'radius_meters' => 50,
                'is_active' => true,
            ]
        );

        $kkBengkong = Branch::updateOrCreate(
            ['code' => 'KK-BENGKONG'],
            [
                'name' => 'Kantor Kas Bengkong',
                'type' => 'Kas',
                'city' => 'Bengkong',
                'address' => 'Komp. Ruko Golden City Blok A No. 5-6, Bengkong, Batam',
                'phone' => '(0778) 4888152',
                'latitude' => 1.16100000,
                'longitude' => 104.03200000,
                'radius_meters' => 50,
                'is_active' => true,
            ]
        );

        $kkTiban = Branch::updateOrCreate(
            ['code' => 'KK-TIBAN'],
            [
                'name' => 'Kantor Kas Tiban',
                'type' => 'Kas',
                'city' => 'Sekupang',
                'address' => 'Ruko Tiban Centre Blok A No. 3-4, Tiban, Sekupang, Batam',
                'phone' => '(0778) 321550',
                'latitude' => 1.10900000,
                'longitude' => 103.96800000,
                'radius_meters' => 50,
                'is_active' => true,
            ]
        );

        // =========================================================================
        // 2. DIVISI DAN UNIT KERJA (DEPARTMENTS)
        // =========================================================================
        $deptDir = Department::updateOrCreate(
            ['code' => 'DIR'],
            ['name' => 'Dewan Direksi & Eksekutif', 'description' => 'Kepemimpinan Strategis & Pengambilan Keputusan Tertinggi Bank']
        );

        $deptHc = Department::updateOrCreate(
            ['code' => 'HC'],
            ['name' => 'Human Capital & GA', 'description' => 'Pengelolaan Sumber Daya Manusia, Remunerasi, Pelatihan & Urusan Umum']
        );

        $deptOps = Department::updateOrCreate(
            ['code' => 'OPS'],
            ['name' => 'Operasional & Layanan Kas', 'description' => 'Pelayanan Frontliner, Customer Service, Teller, Kas Titipan & Kliring']
        );

        $deptLend = Department::updateOrCreate(
            ['code' => 'LEND'],
            ['name' => 'Bisnis & Penyaluran Kredit', 'description' => 'Pembiayaan UMKM, Kredit Modal Kerja, Konsumer & Analisa Agunan']
        );

        $deptRisk = Department::updateOrCreate(
            ['code' => 'RISK'],
            ['name' => 'Kepatuhan & Manajemen Risiko', 'description' => 'Kepatuhan Regulasi OJK/BI, Mitigasi Risiko Kredit & Operasional']
        );

        $deptSkai = Department::updateOrCreate(
            ['code' => 'SKAI'],
            ['name' => 'Satuan Kerja Audit Intern (SKAI)', 'description' => 'Pengawasan & Audit Kepatuhan Internal Operasional Bank']
        );

        $deptIt = Department::updateOrCreate(
            ['code' => 'IT'],
            ['name' => 'Teknologi Informasi & Core Banking', 'description' => 'Infrastruktur TI, Jaringan, Keamanan Data & Core Banking System']
        );

        // =========================================================================
        // 3. JABATAN DAN GRADING (POSITIONS & GRADES)
        // =========================================================================
        $posDirut = Position::updateOrCreate(
            ['name' => 'Direktur Utama', 'department_id' => $deptDir->id],
            ['grade_level' => 'Division Head', 'min_salary' => 30000000, 'max_salary' => 50000000]
        );

        $posDirops = Position::updateOrCreate(
            ['name' => 'Direktur Operasional & Kepatuhan', 'department_id' => $deptDir->id],
            ['grade_level' => 'Division Head', 'min_salary' => 25000000, 'max_salary' => 40000000]
        );

        $posPincab = Position::updateOrCreate(
            ['name' => 'Pemimpin Cabang (Pincab)', 'department_id' => $deptOps->id],
            ['grade_level' => 'Manager', 'min_salary' => 18000000, 'max_salary' => 28000000]
        );

        $posHcMgr = Position::updateOrCreate(
            ['name' => 'Human Capital Manager', 'department_id' => $deptHc->id],
            ['grade_level' => 'Manager', 'min_salary' => 16000000, 'max_salary' => 25000000]
        );

        $posLendMgr = Position::updateOrCreate(
            ['name' => 'Head of Business & Lending', 'department_id' => $deptLend->id],
            ['grade_level' => 'Manager', 'min_salary' => 16000000, 'max_salary' => 25000000]
        );

        $posRiskOfficer = Position::updateOrCreate(
            ['name' => 'Risk & Compliance Officer', 'department_id' => $deptRisk->id],
            ['grade_level' => 'Officer', 'min_salary' => 8500000, 'max_salary' => 15000000]
        );

        $posAuditOfficer = Position::updateOrCreate(
            ['name' => 'Internal Audit Officer', 'department_id' => $deptSkai->id],
            ['grade_level' => 'Officer', 'min_salary' => 8500000, 'max_salary' => 15000000]
        );

        $posItOfficer = Position::updateOrCreate(
            ['name' => 'IT System & Core Banking Officer', 'department_id' => $deptIt->id],
            ['grade_level' => 'Officer', 'min_salary' => 8000000, 'max_salary' => 14000000]
        );

        $posHcOfficer = Position::updateOrCreate(
            ['name' => 'HC & Payroll Officer', 'department_id' => $deptHc->id],
            ['grade_level' => 'Officer', 'min_salary' => 7500000, 'max_salary' => 12000000]
        );

        $posSeniorAo = Position::updateOrCreate(
            ['name' => 'Senior Account Officer (AO)', 'department_id' => $deptLend->id],
            ['grade_level' => 'Officer', 'min_salary' => 7000000, 'max_salary' => 12500000]
        );

        $posSupervisorKas = Position::updateOrCreate(
            ['name' => 'Supervisor Layanan Kas', 'department_id' => $deptOps->id],
            ['grade_level' => 'Supervisor', 'min_salary' => 7000000, 'max_salary' => 11000000]
        );

        $posAoMikro = Position::updateOrCreate(
            ['name' => 'Account Officer (AO) Mikro', 'department_id' => $deptLend->id],
            ['grade_level' => 'Staff', 'min_salary' => 5500000, 'max_salary' => 9000000]
        );

        $posCs = Position::updateOrCreate(
            ['name' => 'Customer Service (CS)', 'department_id' => $deptOps->id],
            ['grade_level' => 'Staff', 'min_salary' => 5200000, 'max_salary' => 8500000]
        );

        $posTeller = Position::updateOrCreate(
            ['name' => 'Teller Kas & Kliring', 'department_id' => $deptOps->id],
            ['grade_level' => 'Staff', 'min_salary' => 5200000, 'max_salary' => 8500000]
        );

        $posAdminKredit = Position::updateOrCreate(
            ['name' => 'Staf Administrasi Kredit & Jaminan', 'department_id' => $deptLend->id],
            ['grade_level' => 'Staff', 'min_salary' => 5200000, 'max_salary' => 8500000]
        );

        // =========================================================================
        // 4. HAK CUTI POKOK (LEAVE TYPES)
        // =========================================================================
        $leaveAnnual = LeaveType::updateOrCreate(
            ['code' => 'ANNUAL'],
            ['name' => 'Cuti Tahunan', 'default_days' => 12, 'is_block_leave' => false, 'max_consecutive_days' => 5, 'description' => 'Hak cuti tahunan reguler bagi karyawan aktif BPR Banda Raya.']
        );

        LeaveType::updateOrCreate(
            ['code' => 'SICK'],
            ['name' => 'Cuti Sakit', 'default_days' => 14, 'is_block_leave' => false, 'description' => 'Izin sakit resmi dengan melampirkan surat keterangan dokter.']
        );

        LeaveType::updateOrCreate(
            ['code' => 'MATERNITY'],
            ['name' => 'Cuti Melahirkan', 'default_days' => 90, 'is_block_leave' => false, 'max_consecutive_days' => 90, 'gender_restriction' => 'female', 'description' => 'Cuti melahirkan bagi staf wanita selama 3 bulan.']
        );

        LeaveType::updateOrCreate(
            ['code' => 'PATERNITY'],
            ['name' => 'Cuti Mendampingi Istri Melahirkan', 'default_days' => 2, 'is_block_leave' => false, 'max_consecutive_days' => 2, 'gender_restriction' => 'male', 'description' => 'Hak cuti mendampingi persalinan bagi staf pria.']
        );

        LeaveType::updateOrCreate(
            ['code' => 'BEREAVEMENT'],
            ['name' => 'Cuti Duka', 'default_days' => 3, 'is_block_leave' => false, 'max_consecutive_days' => 3, 'description' => 'Cuti kedukaan anggota keluarga inti.']
        );

        LeaveType::updateOrCreate(
            ['code' => 'LONG_LEAVE'],
            ['name' => 'Cuti Besar', 'default_days' => 6, 'is_block_leave' => false, 'max_consecutive_days' => 6, 'description' => 'Cuti besar loyalitas bagi karyawan yang telah bekerja minimal 5 tahun (durasi 6 hari kerja).']
        );

        LeaveType::updateOrCreate(
            ['code' => 'BLOCK_LEAVE'],
            ['name' => 'Block Leave (Wajib OJK)', 'default_days' => 5, 'is_block_leave' => true, 'min_consecutive_days' => 5, 'max_consecutive_days' => 5, 'description' => 'Kepatuhan regulasi perbankan OJK: Cuti berturut-turut minimal 5 hari kerja tanpa interupsi akses sistem.']
        );

        // =========================================================================
        // 5. MANAJEMEN AKUN (USERS) & DATA MASTER PEGAWAI (EMPLOYEES)
        // =========================================================================

        // Pastikan Akun Administrator IT Utama tetap ada & terhubung
        $userAdmin = User::firstOrCreate(
            ['email' => 'admin@bprbandaraya.com'],
            [
                'name' => 'Administrator IT BPR',
                'password' => $defaultPassword,
                'role' => 'super_admin',
                'branch_id' => $ho->id,
            ]
        );

        $employeesData = [
            // 1. Direktur Utama (Pusat, Grade 10)
            [
                'nip' => 'BPR-DIR-0001',
                'nik' => '2171011504720001',
                'name' => 'Hendra Gunawan',
                'email' => 'direktur.utama@bprbandaraya.com',
                'role' => 'direksi',
                'branch' => $ho,
                'department' => $deptDir,
                'position' => $posDirut,
                'grade' => 10,
                'gender' => 'Male',
                'birth_date' => '1972-04-15',
                'birth_place' => 'Padang',
                'address' => 'Villa Bukit Indah Blok A No. 12, Sukajadi, Batam',
                'phone' => '081177001001',
                'employment_status' => 'Permanent',
                'join_date' => '2015-02-01',
                'education_level' => 'S-2',
                'education_major' => 'Magister Manajemen Perbankan',
                'education_institution' => 'Universitas Andalas',
                'ipk' => 3.85,
                'npwp' => '71.234.567.8-215.000',
                'ptkp' => 'K/2',
                'emergency_name' => 'Hj. Ratna Juwita',
                'emergency_phone' => '081177001002',
                'emergency_rel' => 'Istri',
            ],
            // 2. Direktur Operasional & Kepatuhan (Pusat, Grade 9)
            [
                'nip' => 'BPR-DIR-0002',
                'nik' => '2171025008770002',
                'name' => 'Rina Marlina',
                'email' => 'direksi@bprbandaraya.com',
                'role' => 'direksi',
                'branch' => $ho,
                'department' => $deptDir,
                'position' => $posDirops,
                'grade' => 9,
                'gender' => 'Female',
                'birth_date' => '1977-08-20',
                'birth_place' => 'Medan',
                'address' => 'Komp. Beverly Park Blok C No. 5, Batam Centre, Batam',
                'phone' => '081270002002',
                'employment_status' => 'Permanent',
                'join_date' => '2017-06-15',
                'education_level' => 'S-2',
                'education_major' => 'Magister Akuntansi & Keuangan',
                'education_institution' => 'Universitas Sumatera Utara',
                'ipk' => 3.78,
                'npwp' => '72.345.678.9-215.000',
                'ptkp' => 'K/1',
                'emergency_name' => 'Dr. Bambang Irawan',
                'emergency_phone' => '081270002003',
                'emergency_rel' => 'Suami',
            ],
            // 3. Human Capital Manager (Pusat, Grade 8)
            [
                'nip' => 'BPR-HC-0001',
                'nik' => '2171011203830003',
                'name' => 'Budi Santoso',
                'email' => 'hrd@bprbandaraya.com',
                'role' => 'hr_admin',
                'branch' => $ho,
                'department' => $deptHc,
                'position' => $posHcMgr,
                'grade' => 8,
                'gender' => 'Male',
                'birth_date' => '1983-03-12',
                'birth_place' => 'Yogyakarta',
                'address' => 'Komp. Orchid Park Blok D No. 8, Batam Centre, Batam',
                'phone' => '081364003003',
                'employment_status' => 'Permanent',
                'join_date' => '2019-01-10',
                'education_level' => 'S-1',
                'education_major' => 'Psikologi Industri & Organisasi',
                'education_institution' => 'Universitas Gadjah Mada',
                'ipk' => 3.65,
                'npwp' => '73.456.789.0-215.000',
                'ptkp' => 'K/2',
                'emergency_name' => 'Sri Rahayu',
                'emergency_phone' => '081364003004',
                'emergency_rel' => 'Istri',
            ],
            // 4. Pemimpin Cabang Botania (Cabang, Grade 8)
            [
                'nip' => 'BPR-CAB-0001',
                'nik' => '2171021406850004',
                'name' => 'Ahmad Fauzi',
                'email' => 'pincab.botania@bprbandaraya.com',
                'role' => 'branch_manager',
                'branch' => $kcBotania,
                'department' => $deptOps,
                'position' => $posPincab,
                'grade' => 8,
                'gender' => 'Male',
                'birth_date' => '1985-06-14',
                'birth_place' => 'Batam',
                'address' => 'Komp. Botania Garden Blok C No. 10, Batam Centre, Batam',
                'phone' => '081277114004',
                'employment_status' => 'Permanent',
                'join_date' => '2018-03-01',
                'education_level' => 'S-1',
                'education_major' => 'Manajemen Keuangan Perbankan',
                'education_institution' => 'Universitas Riau',
                'ipk' => 3.52,
                'npwp' => '74.567.890.1-215.000',
                'ptkp' => 'K/1',
                'emergency_name' => 'Nur Aini',
                'emergency_phone' => '081277114005',
                'emergency_rel' => 'Istri',
            ],
            // 5. Pemimpin Cabang Aviari (Cabang, Grade 8)
            [
                'nip' => 'BPR-CAB-0002',
                'nik' => '2171032209840005',
                'name' => 'Surya Darmawan',
                'email' => 'pincab.aviari@bprbandaraya.com',
                'role' => 'branch_manager',
                'branch' => $kcAviari,
                'department' => $deptOps,
                'position' => $posPincab,
                'grade' => 8,
                'gender' => 'Male',
                'birth_date' => '1984-09-22',
                'birth_place' => 'Tanjung Pinang',
                'address' => 'Griya Batu Aji Asri Blok E No. 15, Batu Aji, Batam',
                'phone' => '081372555005',
                'employment_status' => 'Permanent',
                'join_date' => '2018-08-15',
                'education_level' => 'S-1',
                'education_major' => 'Ekonomi Pembangunan',
                'education_institution' => 'Universitas Maritim Raja Ali Haji',
                'ipk' => 3.48,
                'npwp' => '75.678.901.2-215.000',
                'ptkp' => 'K/2',
                'emergency_name' => 'Maya Sari',
                'emergency_phone' => '081372555006',
                'emergency_rel' => 'Istri',
            ],
            // 6. HC & Payroll Officer (Pusat, Grade 6)
            [
                'nip' => 'BPR-HC-0002',
                'nik' => '2171016505960006',
                'name' => 'Dewi Anggraini',
                'email' => 'dewi.anggraini@bprbandaraya.com',
                'role' => 'hr_admin',
                'branch' => $ho,
                'department' => $deptHc,
                'position' => $posHcOfficer,
                'grade' => 6,
                'gender' => 'Female',
                'birth_date' => '1996-05-25',
                'birth_place' => 'Batam',
                'address' => 'Perum Plamo Garden Blok B No. 22, Batam Centre, Batam',
                'phone' => '081299887711',
                'employment_status' => 'Permanent',
                'join_date' => '2022-01-10',
                'education_level' => 'S-1',
                'education_major' => 'Manajemen SDM',
                'education_institution' => 'Universitas Internasional Batam',
                'ipk' => 3.75,
                'npwp' => '76.789.012.3-215.000',
                'ptkp' => 'TK/0',
                'emergency_name' => 'Drs. H. Suwandi',
                'emergency_phone' => '081299887712',
                'emergency_rel' => 'Ayah',
            ],
            // 7. IT Core Banking Officer (Pusat, Grade 6)
            [
                'nip' => 'BPR-IT-0002',
                'nik' => '2171021811950007',
                'name' => 'Doni Kurniawan',
                'email' => 'doni.it@bprbandaraya.com',
                'role' => 'employee',
                'branch' => $ho,
                'department' => $deptIt,
                'position' => $posItOfficer,
                'grade' => 6,
                'gender' => 'Male',
                'birth_date' => '1995-11-18',
                'birth_place' => 'Padang',
                'address' => 'Komp. Grand BSI Blok D No. 7, Batam Centre, Batam',
                'phone' => '081266334455',
                'employment_status' => 'Permanent',
                'join_date' => '2021-04-01',
                'education_level' => 'S-1',
                'education_major' => 'Sistem Informasi',
                'education_institution' => 'Universitas Putra Indonesia YPTK',
                'ipk' => 3.68,
                'npwp' => '77.890.123.4-215.000',
                'ptkp' => 'TK/0',
                'emergency_name' => 'Eka Saputri',
                'emergency_phone' => '081266334456',
                'emergency_rel' => 'Istri',
            ],
            // 8. Risk & Compliance Officer (Pusat, Grade 6)
            [
                'nip' => 'BPR-RSK-0001',
                'nik' => '2171012307940008',
                'name' => 'Hendra Pratama',
                'email' => 'hendra.risk@bprbandaraya.com',
                'role' => 'employee',
                'branch' => $ho,
                'department' => $deptRisk,
                'position' => $posRiskOfficer,
                'grade' => 6,
                'gender' => 'Male',
                'birth_date' => '1994-07-23',
                'birth_place' => 'Pekanbaru',
                'address' => 'Komp. Palm Spring Blok E No. 9, Batam Centre, Batam',
                'phone' => '081377881122',
                'employment_status' => 'Permanent',
                'join_date' => '2021-07-15',
                'education_level' => 'S-1',
                'education_major' => 'Ilmu Hukum Perdata & Bisnis',
                'education_institution' => 'Universitas Riau',
                'ipk' => 3.55,
                'npwp' => '78.901.234.5-215.000',
                'ptkp' => 'K/1',
                'emergency_name' => 'Dini Oktaviani',
                'emergency_phone' => '081377881123',
                'emergency_rel' => 'Istri',
            ],
            // 9. Senior Account Officer (Botania, Grade 5)
            [
                'nip' => 'BPR-LND-0001',
                'nik' => '2171021004930009',
                'name' => 'Rizky Pratama',
                'email' => 'rizky.ao@bprbandaraya.com',
                'role' => 'employee',
                'branch' => $kcBotania,
                'department' => $deptLend,
                'position' => $posSeniorAo,
                'grade' => 5,
                'gender' => 'Male',
                'birth_date' => '1993-04-10',
                'birth_place' => 'Batam',
                'address' => 'Perum KDA Blok D No. 14, Batam Centre, Batam',
                'phone' => '081270554433',
                'employment_status' => 'Permanent',
                'join_date' => '2021-02-01',
                'education_level' => 'S-1',
                'education_major' => 'Manajemen Keuangan',
                'education_institution' => 'Universitas Internasional Batam',
                'ipk' => 3.42,
                'npwp' => '79.012.345.6-215.000',
                'ptkp' => 'K/1',
                'emergency_name' => 'Anisa Rahmawati',
                'emergency_phone' => '081270554434',
                'emergency_rel' => 'Istri',
            ],
            // 10. Customer Service (Pusat, Grade 4)
            [
                'nip' => 'BPR-OPS-0001',
                'nik' => '2171015509980010',
                'name' => 'Mega Utami',
                'email' => 'mega.cs@bprbandaraya.com',
                'role' => 'employee',
                'branch' => $ho,
                'department' => $deptOps,
                'position' => $posCs,
                'grade' => 4,
                'gender' => 'Female',
                'birth_date' => '1998-09-15',
                'birth_place' => 'Batam',
                'address' => 'Komp. Baloi Kusuma Indah Blok B No. 3, Nagoya, Batam',
                'phone' => '081366112233',
                'employment_status' => 'Permanent',
                'join_date' => '2023-01-05',
                'education_level' => 'D-3',
                'education_major' => 'Manajemen Bisnis Terapan',
                'education_institution' => 'Politeknik Negeri Batam',
                'ipk' => 3.62,
                'npwp' => '80.123.456.7-215.000',
                'ptkp' => 'TK/0',
                'emergency_name' => 'Hj. Syarifah',
                'emergency_phone' => '081366112234',
                'emergency_rel' => 'Ibu',
            ],
            // 11. Teller Kas & Kliring (Pusat, Grade 4)
            [
                'nip' => 'BPR-OPS-0002',
                'nik' => '2171022002970011',
                'name' => 'Bagas Saputra',
                'email' => 'bagas.teller@bprbandaraya.com',
                'role' => 'employee',
                'branch' => $ho,
                'department' => $deptOps,
                'position' => $posTeller,
                'grade' => 4,
                'gender' => 'Male',
                'birth_date' => '1997-02-20',
                'birth_place' => 'Tanjung Balai Karimun',
                'address' => 'Perumahan Tiban Indah Blok R No. 11, Sekupang, Batam',
                'phone' => '081277889900',
                'employment_status' => 'Permanent',
                'join_date' => '2023-03-01',
                'education_level' => 'S-1',
                'education_major' => 'Akuntansi Keuangan',
                'education_institution' => 'Universitas Riau Kepulauan',
                'ipk' => 3.38,
                'npwp' => '81.234.567.8-215.001',
                'ptkp' => 'TK/0',
                'emergency_name' => 'Rahmat Saputra',
                'emergency_phone' => '081277889901',
                'emergency_rel' => 'Ayah',
            ],
            // 12. Teller Kas (Botania, Grade 4)
            [
                'nip' => 'BPR-OPS-0003',
                'nik' => '2171025508980012',
                'name' => 'Siti Rahmadani',
                'email' => 'siti.teller@bprbandaraya.com',
                'role' => 'employee',
                'branch' => $kcBotania,
                'department' => $deptOps,
                'position' => $posTeller,
                'grade' => 4,
                'gender' => 'Female',
                'birth_date' => '1998-08-15',
                'birth_place' => 'Padang',
                'address' => 'Komp. Botania Garden Blok B No. 5, Batam Centre, Batam',
                'phone' => '081377889922',
                'employment_status' => 'Contract',
                'join_date' => '2024-06-01',
                'education_level' => 'D-3',
                'education_major' => 'Perbankan & Keuangan',
                'education_institution' => 'Politeknik Negeri Padang',
                'ipk' => 3.60,
                'npwp' => '82.345.678.9-215.000',
                'ptkp' => 'TK/0',
                'emergency_name' => 'Hj. Maryam',
                'emergency_phone' => '081366551122',
                'emergency_rel' => 'Ibu',
            ],
            // 13. Account Officer Mikro (Aviari, Grade 4)
            [
                'nip' => 'BPR-LND-0002',
                'nik' => '2171031401990013',
                'name' => 'Fajar Hidayat',
                'email' => 'fajar.ao@bprbandaraya.com',
                'role' => 'employee',
                'branch' => $kcAviari,
                'department' => $deptLend,
                'position' => $posAoMikro,
                'grade' => 4,
                'gender' => 'Male',
                'birth_date' => '1999-01-14',
                'birth_place' => 'Batam',
                'address' => 'Komp. Aviari Pratama Blok G No. 8, Batu Aji, Batam',
                'phone' => '081270998877',
                'employment_status' => 'Contract',
                'join_date' => '2024-03-01',
                'education_level' => 'S-1',
                'education_major' => 'Manajemen Bisnis',
                'education_institution' => 'Politeknik Negeri Batam',
                'ipk' => 3.45,
                'npwp' => '83.456.789.0-215.000',
                'ptkp' => 'TK/0',
                'emergency_name' => 'H. Hidayatullah',
                'emergency_phone' => '081270998878',
                'emergency_rel' => 'Ayah',
            ],
            // 14. Customer Service (Kas Bengkong, Grade 4)
            [
                'nip' => 'BPR-OPS-0004',
                'nik' => '2171016007000014',
                'name' => 'Nurul Hasanah',
                'email' => 'nurul.cs@bprbandaraya.com',
                'role' => 'employee',
                'branch' => $kkBengkong,
                'department' => $deptOps,
                'position' => $posCs,
                'grade' => 4,
                'gender' => 'Female',
                'birth_date' => '2000-07-20',
                'birth_place' => 'Tanjung Pinang',
                'address' => 'Komp. Bengkong Harapan Blok F No. 2, Bengkong, Batam',
                'phone' => '081372661144',
                'employment_status' => 'Contract',
                'join_date' => '2024-07-15',
                'education_level' => 'S-1',
                'education_major' => 'Akuntansi',
                'education_institution' => 'Universitas Internasional Batam',
                'ipk' => 3.70,
                'npwp' => '84.567.890.1-215.000',
                'ptkp' => 'TK/0',
                'emergency_name' => 'Hasan Basri',
                'emergency_phone' => '081372661145',
                'emergency_rel' => 'Ayah',
            ],
            // 15. Customer Service (Kas Tiban, Grade 4)
            [
                'nip' => 'BPR-OPS-0005',
                'nik' => '2171014502010015',
                'name' => 'Lestari Wulandari',
                'email' => 'lestari.cs@bprbandaraya.com',
                'role' => 'employee',
                'branch' => $kkTiban,
                'department' => $deptOps,
                'position' => $posCs,
                'grade' => 4,
                'gender' => 'Female',
                'birth_date' => '2001-02-05',
                'birth_place' => 'Batam',
                'address' => 'Ruko Tiban Mas Asri Blok C No. 6, Tiban, Sekupang, Batam',
                'phone' => '081277443322',
                'employment_status' => 'Contract',
                'join_date' => '2024-09-01',
                'education_level' => 'S-1',
                'education_major' => 'Manajemen Perbankan',
                'education_institution' => 'Universitas Riau Kepulauan',
                'ipk' => 3.58,
                'npwp' => '85.678.901.2-215.000',
                'ptkp' => 'TK/0',
                'emergency_name' => 'Wulandari Putri',
                'emergency_phone' => '081277443323',
                'emergency_rel' => 'Ibu',
            ],
        ];

        $createdEmployees = [];

        foreach ($employeesData as $idx => $d) {
            // Buat akun User
            $user = User::updateOrCreate(
                ['email' => $d['email']],
                [
                    'name' => $d['name'],
                    'password' => $defaultPassword,
                    'role' => $d['role'],
                    'branch_id' => $d['branch']->id,
                ]
            );

            // Buat profil Master Pegawai
            $emp = Employee::updateOrCreate(
                ['nip' => $d['nip']],
                [
                    'user_id' => $user->id,
                    'nik' => $d['nik'],
                    'full_name' => $d['name'],
                    'email' => $d['email'],
                    'personal_email' => 'personal.' . strtolower(explode(' ', $d['name'])[0]) . '@gmail.com',
                    'phone' => $d['phone'],
                    'gender' => $d['gender'],
                    'birth_date' => $d['birth_date'],
                    'birth_place' => $d['birth_place'],
                    'address' => $d['address'],
                    'branch_id' => $d['branch']->id,
                    'department_id' => $d['department']->id,
                    'position_id' => $d['position']->id,
                    'job_effective_date' => $d['join_date'],
                    'grade' => $d['grade'],
                    'grade_effective_date' => $d['join_date'],
                    'employment_status' => $d['employment_status'],
                    'join_date' => $d['join_date'],
                    'permanent_date' => $d['employment_status'] === 'Permanent' ? $d['join_date'] : null,
                    'contract_end_date' => $d['employment_status'] === 'Contract' ? Carbon::parse($d['join_date'])->addYears(2)->toDateString() : null,
                    'status' => 'Active',
                    'education_level' => $d['education_level'],
                    'education_major' => $d['education_major'],
                    'education_institution' => $d['education_institution'],
                    'ipk' => $d['ipk'],
                    'bank_bpr_account' => '01.10.' . sprintf('%05d', ($idx + 1) * 317 + 100),
                    'bank_external_name' => ['Bank Mandiri', 'BCA', 'BNI'][$idx % 3],
                    'bank_external_account' => '109-00-' . rand(1000000, 9999999),
                    'bank_account_number' => '01.10.' . sprintf('%05d', ($idx + 1) * 317 + 100),
                    'bank_account_holder' => $d['name'],
                    'npwp' => $d['npwp'],
                    'bpjs_tk_number' => '080' . rand(10000000, 99999999),
                    'bpjs_kes_number' => '0001' . rand(10000000, 99999999),
                    'ptkp_status' => $d['ptkp'],
                    'emergency_contact_name' => $d['emergency_name'],
                    'emergency_contact_phone' => $d['emergency_phone'],
                    'emergency_contact_relation' => $d['emergency_rel'],
                ]
            );

            // Inisialisasi saldo cuti tahunan
            EmployeeLeaveBalance::updateOrCreate(
                [
                    'employee_id' => $emp->id,
                    'leave_type_id' => $leaveAnnual->id,
                    'year' => (int) date('Y'),
                ],
                [
                    'initial_quota' => 12,
                    'used_quota' => 0,
                    'remaining_quota' => 12,
                ]
            );

            $createdEmployees[] = $emp;
        }

        // =========================================================================
        // 6. DATA FIKTIF ABSEN (ATTENDANCE LOGS - BULAN OKTOBER 2026)
        // =========================================================================
        // Bersihkan data presensi lama agar tidak duplikat
        Attendance::truncate();

        $year = 2026;
        $month = 10;
        $todayDay = 15; // Hingga tanggal 15 Oktober 2026 (hari ini)

        foreach ($createdEmployees as $empIndex => $emp) {
            for ($d = 1; $d <= $todayDay; $d++) {
                $dateObj = Carbon::create($year, $month, $d);

                // Hari Minggu libur operasional bank (tidak ada presensi)
                if ($dateObj->isSunday()) {
                    continue;
                }

                $dateStr = $dateObj->toDateString();

                // Variasi Skenario Kehadiran Realistis:
                // Skenario 1: Cuti Resmi (misal: Siti Rahmadani pada 8 & 9 Oktober)
                if ($emp->nip === 'BPR-OPS-0003' && in_array($d, [8, 9])) {
                    Attendance::create([
                        'employee_id' => $emp->id,
                        'date' => $dateStr,
                        'clock_in' => null,
                        'clock_out' => null,
                        'status' => 'Excused',
                        'late_minutes' => 0,
                        'penalty_amount' => 0,
                        'notes' => 'Cuti Resmi Tahunan (Disetujui HRD & Pincab)',
                    ]);
                    continue;
                }

                // Skenario 2: Izin Tugas Luar / Rapat Koordinasi OJK (misal: Pincab Botania pada 12 Oktober)
                if ($emp->nip === 'BPR-CAB-0001' && $d === 12) {
                    Attendance::create([
                        'employee_id' => $emp->id,
                        'date' => $dateStr,
                        'clock_in' => null,
                        'clock_out' => null,
                        'status' => 'Excused',
                        'late_minutes' => 0,
                        'penalty_amount' => 0,
                        'notes' => 'Dispensasi Rapat Koordinasi Perbarindo & OJK Kepri',
                    ]);
                    continue;
                }

                // Skenario 3: Dispensasi Telat Masuk Sah (misal: CS Mega Utami pada 13 Oktober - hadir tapi izin telat)
                if ($emp->nip === 'BPR-OPS-0001' && $d === 13) {
                    Attendance::create([
                        'employee_id' => $emp->id,
                        'date' => $dateStr,
                        'clock_in' => "{$dateStr} 08:35:00",
                        'clock_out' => "{$dateStr} 17:15:00",
                        'status' => 'Present',
                        'late_minutes' => 0,
                        'penalty_amount' => 0,
                        'notes' => 'Hadir (Dispensasi Telat Masuk HRD - Kendala Cuaca Hujan Deras)',
                    ]);
                    continue;
                }

                // Skenario 4: Keterlambatan Riil (Terlambat & Denda Rp 50.000)
                // Teller Bagas Saputra telat di tanggal 6 Oktober (masuk 08:18)
                if ($emp->nip === 'BPR-OPS-0002' && $d === 6) {
                    Attendance::create([
                        'employee_id' => $emp->id,
                        'date' => $dateStr,
                        'clock_in' => "{$dateStr} 08:18:00",
                        'clock_out' => "{$dateStr} 17:12:00",
                        'status' => 'Late',
                        'late_minutes' => 18,
                        'penalty_amount' => 50000,
                        'notes' => 'Keterlambatan 18 menit (Pencatatan Otomatis Mesin)',
                    ]);
                    continue;
                }

                // AO Rizky Pratama telat di tanggal 9 Oktober (masuk 08:24)
                if ($emp->nip === 'BPR-LND-0001' && $d === 9) {
                    Attendance::create([
                        'employee_id' => $emp->id,
                        'date' => $dateStr,
                        'clock_in' => "{$dateStr} 08:24:00",
                        'clock_out' => "{$dateStr} 17:30:00",
                        'status' => 'Late',
                        'late_minutes' => 24,
                        'penalty_amount' => 50000,
                        'notes' => 'Keterlambatan 24 menit (Pencatatan Otomatis Mesin)',
                    ]);
                    continue;
                }

                // Skenario 5: Kehadiran Tepat Waktu (Present - 95% Hari Kerja)
                // Jam masuk bervariasi antara 07:42 s.d 07:58
                $inMinute = str_pad(rand(42, 58), 2, '0', STR_PAD_LEFT);
                $inSecond = str_pad(rand(10, 55), 2, '0', STR_PAD_LEFT);
                $clockInTime = "07:{$inMinute}:{$inSecond}";

                // Jam pulang bervariasi antara 17:05 s.d 17:35
                $outMinute = str_pad(rand(5, 35), 2, '0', STR_PAD_LEFT);
                $outSecond = str_pad(rand(10, 55), 2, '0', STR_PAD_LEFT);
                $clockOutTime = "17:{$outMinute}:{$outSecond}";

                Attendance::create([
                    'employee_id' => $emp->id,
                    'date' => $dateStr,
                    'clock_in' => "{$dateStr} {$clockInTime}",
                    'clock_out' => "{$dateStr} {$clockOutTime}",
                    'status' => 'Present',
                    'late_minutes' => 0,
                    'penalty_amount' => 0,
                    'notes' => 'Tarik Log Mesin Fingerprint Biometrik Solution',
                ]);
            }
        }
    }
}
