<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Payroll;
use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('email', 'admin@bprbandaraya.com')->exists()) {
            return;
        }

        // 1. Seed Branches (Sesuai Data Resmi BPR Banda Raya Batam)
        $ho = Branch::create([
            'code' => 'HO-001',
            'name' => 'Kantor Pusat BPR Banda Raya',
            'type' => 'Head Office',
            'city' => 'Batam',
            'address' => 'Komplek Pertokoan Nagoya Hill Blok G No. 1-3, Batam',
            'phone' => '(0778) 424100',
            'latitude' => 1.14410000,
            'longitude' => 104.01350000,
            'radius_meters' => 100,
            'is_active' => true,
        ]);

        $kcBotania = Branch::create([
            'code' => 'KC-BOTANIA',
            'name' => 'Kantor Cabang Botania',
            'type' => 'Main Branch',
            'city' => 'Batam',
            'address' => 'Komp. Ruko Botania Garden Blok B2 No. 1-2, Batam Centre, Batam',
            'phone' => '(0778) 7495 400',
            'latitude' => 1.11650000,
            'longitude' => 104.08900000,
            'radius_meters' => 50,
            'is_active' => true,
        ]);

        $kcAviari = Branch::create([
            'code' => 'KC-AVIARI',
            'name' => 'Kantor Cabang Aviari',
            'type' => 'Main Branch',
            'city' => 'Batam',
            'address' => 'Komp. Pertokoan Aviari Pratama Blok B No. 12-14, Batu Aji, Batam',
            'phone' => '(0778) 361100',
            'latitude' => 1.05050000,
            'longitude' => 103.97800000,
            'radius_meters' => 50,
            'is_active' => true,
        ]);

        $kcBengkong = Branch::create([
            'code' => 'KC-BENGKONG',
            'name' => 'Kantor Cabang Bengkong',
            'type' => 'Main Branch',
            'city' => 'Batam',
            'address' => 'Komp. Ruko Golden City Blok A No. 5-6, Bengkong, Batam',
            'phone' => '(0778) 4888152',
            'latitude' => 1.16100000,
            'longitude' => 104.03200000,
            'radius_meters' => 50,
            'is_active' => true,
        ]);

        // 2. Seed Departments
        $deptHc = Department::create(['code' => 'HC', 'name' => 'Human Capital & SDM', 'description' => 'Pengelolaan Sumber Daya Manusia & Organisasi BPR']);
        $deptRisk = Department::create(['code' => 'RISK', 'name' => 'Kepatuhan & Manajemen Risiko', 'description' => 'Kepatuhan Regulasi OJK & Mitigasi Risiko Perbankan']);
        $deptOps = Department::create(['code' => 'OPS', 'name' => 'Operasional & Layanan Kas', 'description' => 'Frontliner, Teller, Customer Service, & Kliring']);
        $deptLend = Department::create(['code' => 'LEND', 'name' => 'Kredit & Bisnis Komersial', 'description' => 'Penyaluran Pembiayaan & Analis Kredit UMKM']);
        $deptAudit = Department::create(['code' => 'AUDIT', 'name' => 'Satuan Kerja Audit Internal (SKAI)', 'description' => 'Pemeriksaan & Pengawasan Kepatuhan Internal']);
        $deptIt = Department::create(['code' => 'IT', 'name' => 'Teknologi Informasi & Digital Core', 'description' => 'Core Banking & Infrastruktur TI']);

        // 3. Seed Positions
        $posPincab = Position::create(['department_id' => $deptOps->id, 'name' => 'Pemimpin Cabang (Pincab)', 'grade_level' => 'Manager', 'min_salary' => 20000000, 'max_salary' => 35000000]);
        $posHrManager = Position::create(['department_id' => $deptHc->id, 'name' => 'Human Capital Manager', 'grade_level' => 'Manager', 'min_salary' => 18000000, 'max_salary' => 30000000]);
        $posHrOfficer = Position::create(['department_id' => $deptHc->id, 'name' => 'HC Operations Officer', 'grade_level' => 'Officer', 'min_salary' => 8000000, 'max_salary' => 14000000]);
        $posRiskOfficer = Position::create(['department_id' => $deptRisk->id, 'name' => 'Risk & Compliance Officer', 'grade_level' => 'Officer', 'min_salary' => 9000000, 'max_salary' => 16000000]);
        $posAo = Position::create(['department_id' => $deptLend->id, 'name' => 'Account Officer (AO)', 'grade_level' => 'Officer', 'min_salary' => 7000000, 'max_salary' => 14000000]);
        $posCs = Position::create(['department_id' => $deptOps->id, 'name' => 'Customer Service', 'grade_level' => 'Staff', 'min_salary' => 5500000, 'max_salary' => 8500000]);
        $posTeller = Position::create(['department_id' => $deptOps->id, 'name' => 'Teller Kas & Kliring', 'grade_level' => 'Staff', 'min_salary' => 5500000, 'max_salary' => 8500000]);

        // 4. Seed Leave Types (termasuk Block Leave OJK)
        $leaveAnnual = LeaveType::create([
            'code' => 'ANNUAL',
            'name' => 'Cuti Tahunan',
            'default_days' => 12,
            'is_block_leave' => false,
            'min_consecutive_days' => null,
            'description' => 'Hak cuti tahunan reguler bagi staf tetap/kontrak.',
        ]);

        $leaveBlock = LeaveType::create([
            'code' => 'BLOCK_LEAVE',
            'name' => 'Block Leave (Wajib OJK)',
            'default_days' => 5,
            'is_block_leave' => true,
            'min_consecutive_days' => 5,
            'description' => 'Kepatuhan regulasi perbankan OJK: Cuti berturut-turut minimal 5 hari kerja tanpa interupsi akses sistem.',
        ]);

        $leaveSick = LeaveType::create([
            'code' => 'SICK',
            'name' => 'Cuti Sakit',
            'default_days' => 14,
            'is_block_leave' => false,
            'min_consecutive_days' => null,
            'description' => 'Izin sakit dengan melampirkan surat keterangan dokter.',
        ]);

        $leaveMaternity = LeaveType::create([
            'code' => 'MATERNITY',
            'name' => 'Cuti Melahirkan',
            'default_days' => 90,
            'is_block_leave' => false,
            'min_consecutive_days' => null,
            'description' => 'Cuti melahirkan bagi pegawai perempuan (3 bulan).',
        ]);

        // 5. Seed Users & Employees (Domain: @bprbandaraya.com)
        $password = Hash::make('password');

        // A. Super Admin & Head Office HR
        $uAdmin = User::create([
            'name' => 'Super Administrator BPR Banda Raya',
            'email' => 'admin@bprbandaraya.com',
            'password' => $password,
            'role' => 'super_admin',
            'branch_id' => $ho->id,
        ]);

        $uHr = User::create([
            'name' => 'Ratna Dewi, S.Psi',
            'email' => 'ratna.dewi@bprbandaraya.com',
            'password' => $password,
            'role' => 'hr_admin',
            'branch_id' => $ho->id,
        ]);

        $empHr = Employee::create([
            'user_id' => $uHr->id,
            'nip' => 'BPR-HO-0002',
            'nik' => '2171012304920001',
            'full_name' => 'Ratna Dewi, S.Psi',
            'email' => 'ratna.dewi@bprbandaraya.com',
            'phone' => '081288991122',
            'gender' => 'Female',
            'birth_date' => '1992-04-23',
            'address' => 'Komp. Nagoya City Centre Blok B No. 8, Batam',
            'branch_id' => $ho->id,
            'department_id' => $deptHc->id,
            'position_id' => $posHrOfficer->id,
            'employment_status' => 'Permanent',
            'join_date' => '2020-03-01',
            'status' => 'Active',
            'bank_account_number' => '1200098765432',
            'bank_account_holder' => 'Ratna Dewi',
            'npwp' => '71.234.567.8-012.000',
        ]);

        // B. Pincab KC Botania
        $uPincabBotania = User::create([
            'name' => 'Budi Santoso, S.E., M.M.',
            'email' => 'pincab.botania@bprbandaraya.com',
            'password' => $password,
            'role' => 'branch_manager',
            'branch_id' => $kcBotania->id,
        ]);

        $empPincabBotania = Employee::create([
            'user_id' => $uPincabBotania->id,
            'nip' => 'BPR-BOT-0001',
            'nik' => '2171081508800002',
            'full_name' => 'Budi Santoso, S.E., M.M.',
            'email' => 'pincab.botania@bprbandaraya.com',
            'phone' => '081199887766',
            'gender' => 'Male',
            'birth_date' => '1980-08-15',
            'address' => 'Perumahan Botania Garden Tahap 1 Blok D No. 12, Batam',
            'branch_id' => $kcBotania->id,
            'department_id' => $deptOps->id,
            'position_id' => $posPincab->id,
            'employment_status' => 'Permanent',
            'join_date' => '2015-06-01',
            'status' => 'Active',
            'bank_account_number' => '1200012345678',
            'bank_account_holder' => 'Budi Santoso',
            'npwp' => '70.111.222.3-015.000',
        ]);

        // C. Pincab KC Aviari
        $uPincabAviari = User::create([
            'name' => 'Ahmad Fauzi, S.E.',
            'email' => 'pincab.aviari@bprbandaraya.com',
            'password' => $password,
            'role' => 'branch_manager',
            'branch_id' => $kcAviari->id,
        ]);

        $empPincabAviari = Employee::create([
            'user_id' => $uPincabAviari->id,
            'nip' => 'BPR-AVI-0001',
            'nik' => '2171011211820003',
            'full_name' => 'Ahmad Fauzi, S.E.',
            'email' => 'pincab.aviari@bprbandaraya.com',
            'phone' => '081322334455',
            'gender' => 'Male',
            'birth_date' => '1982-11-12',
            'address' => 'Perumahan Buana Vista Blok A No. 15, Batu Aji, Batam',
            'branch_id' => $kcAviari->id,
            'department_id' => $deptOps->id,
            'position_id' => $posPincab->id,
            'employment_status' => 'Permanent',
            'join_date' => '2017-02-15',
            'status' => 'Active',
            'bank_account_number' => '1200034567890',
            'bank_account_holder' => 'Ahmad Fauzi',
            'npwp' => '72.333.444.5-021.000',
        ]);

        // D. Pincab KC Bengkong
        $uPincabBengkong = User::create([
            'name' => 'Hendra Wijaya, S.E.',
            'email' => 'pincab.bengkong@bprbandaraya.com',
            'password' => $password,
            'role' => 'branch_manager',
            'branch_id' => $kcBengkong->id,
        ]);

        $empPincabBengkong = Employee::create([
            'user_id' => $uPincabBengkong->id,
            'nip' => 'BPR-BNG-0001',
            'nik' => '2171032506850004',
            'full_name' => 'Hendra Wijaya, S.E.',
            'email' => 'pincab.bengkong@bprbandaraya.com',
            'phone' => '081277889900',
            'gender' => 'Male',
            'birth_date' => '1985-06-25',
            'address' => 'Perumahan Bengkong Laut Blok C No. 5, Batam',
            'branch_id' => $kcBengkong->id,
            'department_id' => $deptOps->id,
            'position_id' => $posPincab->id,
            'employment_status' => 'Permanent',
            'join_date' => '2018-04-10',
            'status' => 'Active',
            'bank_account_number' => '1200056789012',
            'bank_account_holder' => 'Hendra Wijaya',
            'npwp' => '73.444.555.6-022.000',
        ]);

        // E. Staff per Cabang (Teller, CS, AO)
        $staffData = [
            // Staff KC Botania
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'teller.botania@bprbandaraya.com',
                'nip' => 'BPR-BOT-0010',
                'branch' => $kcBotania,
                'dept' => $deptOps,
                'pos' => $posTeller,
                'gender' => 'Female',
                'salary' => 6000000,
                'allowance' => 1500000,
                'deduction' => 300000,
            ],
            [
                'name' => 'Rian Pratama',
                'email' => 'cs.botania@bprbandaraya.com',
                'nip' => 'BPR-BOT-0011',
                'branch' => $kcBotania,
                'dept' => $deptOps,
                'pos' => $posCs,
                'gender' => 'Male',
                'salary' => 6500000,
                'allowance' => 1000000,
                'deduction' => 320000,
            ],
            [
                'name' => 'Dimas Anggara',
                'email' => 'ao.botania@bprbandaraya.com',
                'nip' => 'BPR-BOT-0012',
                'branch' => $kcBotania,
                'dept' => $deptLend,
                'pos' => $posAo,
                'gender' => 'Male',
                'salary' => 8500000,
                'allowance' => 2000000,
                'deduction' => 450000,
            ],
            // Staff KC Aviari
            [
                'name' => 'Nabila Putri',
                'email' => 'teller.aviari@bprbandaraya.com',
                'nip' => 'BPR-AVI-0010',
                'branch' => $kcAviari,
                'dept' => $deptOps,
                'pos' => $posTeller,
                'gender' => 'Female',
                'salary' => 5800000,
                'allowance' => 1500000,
                'deduction' => 290000,
            ],
            [
                'name' => 'Fajar Nugraha',
                'email' => 'ao.aviari@bprbandaraya.com',
                'nip' => 'BPR-AVI-0011',
                'branch' => $kcAviari,
                'dept' => $deptLend,
                'pos' => $posAo,
                'gender' => 'Male',
                'salary' => 8500000,
                'allowance' => 1800000,
                'deduction' => 420000,
            ],
            // Staff KC Bengkong
            [
                'name' => 'Jessica Tanuwijaya',
                'email' => 'teller.bengkong@bprbandaraya.com',
                'nip' => 'BPR-BNG-0005',
                'branch' => $kcBengkong,
                'dept' => $deptOps,
                'pos' => $posTeller,
                'gender' => 'Female',
                'salary' => 6000000,
                'allowance' => 1500000,
                'deduction' => 300000,
            ],
            [
                'name' => 'Rizky Ramadhan',
                'email' => 'ao.bengkong@bprbandaraya.com',
                'nip' => 'BPR-BNG-0006',
                'branch' => $kcBengkong,
                'dept' => $deptLend,
                'pos' => $posAo,
                'gender' => 'Male',
                'salary' => 8000000,
                'allowance' => 1800000,
                'deduction' => 380000,
            ],
        ];

        $allEmployees = collect([$empHr, $empPincabBotania, $empPincabAviari, $empPincabBengkong]);

        foreach ($staffData as $item) {
            $user = User::create([
                'name' => $item['name'],
                'email' => $item['email'],
                'password' => $password,
                'role' => 'employee',
                'branch_id' => $item['branch']->id,
            ]);

            $emp = Employee::create([
                'user_id' => $user->id,
                'nip' => $item['nip'],
                'nik' => '2171' . rand(100000000000, 999999999999),
                'full_name' => $item['name'],
                'email' => $item['email'],
                'phone' => '081' . rand(100000000, 999999999),
                'gender' => $item['gender'],
                'birth_date' => '1995-05-10',
                'address' => 'Komp. Ruko ' . $item['branch']->name . ' No. ' . rand(1, 50) . ', Batam',
                'branch_id' => $item['branch']->id,
                'department_id' => $item['dept']->id,
                'position_id' => $item['pos']->id,
                'employment_status' => 'Permanent',
                'join_date' => '2022-01-10',
                'status' => 'Active',
                'bank_account_number' => '120' . rand(1000000000, 9999999999),
                'bank_account_holder' => $item['name'],
                'npwp' => '80.' . rand(100, 999) . '.' . rand(100, 999) . '.0-011.000',
            ]);

            $allEmployees->push($emp);

            // Seed/Update Leave Balances for 2026
            EmployeeLeaveBalance::updateOrCreate(
                [
                    'employee_id' => $emp->id,
                    'leave_type_id' => $leaveAnnual->id,
                    'year' => 2026,
                ],
                [
                    'initial_quota' => 12,
                    'used_quota' => 2,
                    'remaining_quota' => 10,
                ]
            );

            EmployeeLeaveBalance::updateOrCreate(
                [
                    'employee_id' => $emp->id,
                    'leave_type_id' => $leaveBlock->id,
                    'year' => 2026,
                ],
                [
                    'initial_quota' => 5,
                    'used_quota' => 0,
                    'remaining_quota' => 5,
                ]
            );

            // Seed sample attendances for past 3 days
            for ($d = 2; $d >= 0; $d--) {
                $attDate = Carbon::today()->subDays($d);
                if (! $attDate->isWeekend()) {
                    Attendance::create([
                        'employee_id' => $emp->id,
                        'date' => $attDate->toDateString(),
                        'clock_in' => '07:55:00',
                        'clock_out' => '17:05:00',
                        'status' => 'Present',
                        'lat_in' => $item['branch']->latitude,
                        'long_in' => $item['branch']->longitude,
                        'lat_out' => $item['branch']->latitude,
                        'long_out' => $item['branch']->longitude,
                        'distance_in_meters' => rand(5, 25),
                        'notes' => 'Presensi tepat waktu kantor ' . $item['branch']->name,
                    ]);
                }
            }

            // Seed sample payroll (Month 7 & 8 2026)
            $net = $item['salary'] + $item['allowance'] - $item['deduction'];
            Payroll::create([
                'employee_id' => $emp->id,
                'period_month' => 7,
                'period_year' => 2026,
                'basic_salary' => $item['salary'],
                'allowances' => $item['allowance'],
                'deductions' => $item['deduction'],
                'net_salary' => $net,
                'payment_date' => '2026-07-25',
                'status' => 'Paid',
                'notes' => 'Payroll bulan Juli 2026 telah ditransfer ke rekening pegawai.',
            ]);

            Payroll::create([
                'employee_id' => $emp->id,
                'period_month' => 8,
                'period_year' => 2026,
                'basic_salary' => $item['salary'],
                'allowances' => $item['allowance'],
                'deductions' => $item['deduction'],
                'net_salary' => $net,
                'payment_date' => '2026-08-25',
                'status' => 'Approved',
                'notes' => 'Payroll bulan Agustus 2026 siap disbursement.',
            ]);
        }

        // Seed Sample Leave Requests
        $tellerBotaniaEmp = Employee::where('email', 'teller.botania@bprbandaraya.com')->first();
        if ($tellerBotaniaEmp) {
            LeaveRequest::create([
                'employee_id' => $tellerBotaniaEmp->id,
                'leave_type_id' => $leaveAnnual->id,
                'start_date' => Carbon::today()->addDays(5)->toDateString(),
                'end_date' => Carbon::today()->addDays(6)->toDateString(),
                'total_days' => 2,
                'reason' => 'Keperluan keluarga di luar kota',
                'status' => 'Pending',
            ]);

            // Sample Block Leave Request (5 days consecutive)
            LeaveRequest::create([
                'employee_id' => $tellerBotaniaEmp->id,
                'leave_type_id' => $leaveBlock->id,
                'start_date' => Carbon::today()->addDays(15)->toDateString(),
                'end_date' => Carbon::today()->addDays(19)->toDateString(),
                'total_days' => 5,
                'reason' => 'Pengambilan Mandatori Block Leave OJK 2026',
                'status' => 'Approved_Pincab',
                'approved_by' => $uPincabBotania->id,
            ]);
        }

        // 6. Seed Announcements
        Announcement::create([
            'title' => 'Surat Edaran Direksi: Kewajiban Pengambilan Mandatori Block Leave OJK 2026',
            'category' => 'direksi_memo',
            'priority' => 'urgent',
            'content' => 'Diberitahukan kepada seluruh pegawai pemegang posisi berisiko (Teller, Customer Service, Account Officer, dan Pejabat Operasional Cabang) wajib menjadwalkan cuti Block Leave minimal 5 hari kerja berturut-turut sesuai POJK Kepatuhan Tata Kelola Perbankan.',
            'author_id' => $uAdmin->id,
            'is_active' => true,
            'published_at' => Carbon::today()->subDays(2)->toDateString(),
        ]);

        Announcement::create([
            'title' => 'Penerapan Geofencing GPS Presensi Online Kantor Pusat & Cabang Batam',
            'category' => 'ojk_compliance',
            'priority' => 'important',
            'content' => 'Presensi masuk dan pulang wajib dilakukan dalam radius geofencing cabang masing-masing (Kantor Pusat Nagoya 100m, KC Botania 50m, KC Aviari 50m, KC Bengkong 50m). Pastikan GPS perangkat aktif saat melakukan presensi.',
            'author_id' => $uAdmin->id,
            'is_active' => true,
            'published_at' => Carbon::today()->subDays(5)->toDateString(),
        ]);

        Announcement::create([
            'title' => 'Jadwal Libur Nasional & Penyesuaian Jam Operasional Kas Layanan BPR Banda Raya',
            'category' => 'holiday',
            'priority' => 'normal',
            'content' => 'Informasi operasional kliring dan kas layanan kantor cabang selama libur nasional. Seluruh staf operasional diharapkan memperhatikan jadwal piket keamanan dan layanan kas.',
            'author_id' => $uAdmin->id,
            'is_active' => true,
            'published_at' => Carbon::today()->subDays(1)->toDateString(),
        ]);

        // 7. Seed Solution X105 Fingerprint Machines
        \App\Models\FingerprintDevice::create([
            'name' => 'Solution X105 - Kantor Pusat Nagoya',
            'model' => 'Solution X105',
            'serial_number' => 'SOL-X105-HO01',
            'branch_id' => $ho->id,
            'ip_address' => '192.168.1.201',
            'port' => 4370,
            'comm_key' => '0',
            'status' => 'Online',
            'last_sync_at' => Carbon::now()->subMinutes(15),
            'total_logs_count' => 142,
            'is_active' => true,
        ]);

        \App\Models\FingerprintDevice::create([
            'name' => 'Solution X105 - KC Botania Garden',
            'model' => 'Solution X105',
            'serial_number' => 'SOL-X105-BOT01',
            'branch_id' => $kcBotania->id,
            'ip_address' => '192.168.2.201',
            'port' => 4370,
            'comm_key' => '0',
            'status' => 'Online',
            'last_sync_at' => Carbon::now()->subMinutes(30),
            'total_logs_count' => 86,
            'is_active' => true,
        ]);

        \App\Models\FingerprintDevice::create([
            'name' => 'Solution X105 - KC Aviari Pratama',
            'model' => 'Solution X105',
            'serial_number' => 'SOL-X105-AVI01',
            'branch_id' => $kcAviari->id,
            'ip_address' => '192.168.3.201',
            'port' => 4370,
            'comm_key' => '0',
            'status' => 'Online',
            'last_sync_at' => Carbon::now()->subHours(1),
            'total_logs_count' => 64,
            'is_active' => true,
        ]);

        \App\Models\FingerprintDevice::create([
            'name' => 'Solution X105 - KC Bengkong Golden City',
            'model' => 'Solution X105',
            'serial_number' => 'SOL-X105-BNG01',
            'branch_id' => $kcBengkong->id,
            'ip_address' => '192.168.4.201',
            'port' => 4370,
            'comm_key' => '0',
            'status' => 'Online',
            'last_sync_at' => Carbon::now()->subHours(2),
            'total_logs_count' => 52,
            'is_active' => true,
        ]);

        // 8. Update Contract Dates for Sample Alert Testing
        $empTellerBot = Employee::where('email', 'teller.botania@bprbandaraya.com')->first();
        if ($empTellerBot) {
            $empTellerBot->update([
                'employment_status' => 'Contract',
                'contract_end_date' => Carbon::today()->addDays(24), // Alert in 24 days!
            ]);
        }

        $empAoAviari = Employee::where('email', 'ao.aviari@bprbandaraya.com')->first();
        if ($empAoAviari) {
            $empAoAviari->update([
                'employment_status' => 'Probation',
                'probation_end_date' => Carbon::today()->addDays(40), // Alert in 40 days!
            ]);
        }

        // 9. Seed Sample Overtimes (SPK Lembur)
        if ($empTellerBot) {
            \App\Models\Overtime::create([
                'employee_id' => $empTellerBot->id,
                'date' => Carbon::today()->subDays(1)->toDateString(),
                'start_time' => '17:30:00',
                'end_time' => '20:30:00',
                'duration_hours' => 3.00,
                'reason' => 'Tutup Buku Akhir Bulan (EOM Closing) & Rekonsiliasi Kas Teller',
                'status' => 'Approved_Pincab',
                'approved_by' => $uPincabBotania->id,
            ]);

            \App\Models\Overtime::create([
                'employee_id' => $empTellerBot->id,
                'date' => Carbon::today()->toDateString(),
                'start_time' => '17:30:00',
                'end_time' => '19:30:00',
                'duration_hours' => 2.00,
                'reason' => 'Pemberkasan Arsip Dokumen Pembukaan Rekening Nasabah Baru',
                'status' => 'Pending',
            ]);
        }

        // 10. Seed Sample Reimbursements
        if ($empAoAviari) {
            \App\Models\Reimbursement::create([
                'employee_id' => $empAoAviari->id,
                'title' => 'Bensin Kunjungan Calon Debitur UMKM Aviari',
                'category' => 'Bensin & Transport AO',
                'date' => Carbon::today()->subDays(2)->toDateString(),
                'amount' => 125000,
                'description' => 'Survei agunan ruko debitur PT Berkah Mandiri di Batu Aji',
                'status' => 'Approved_Pincab',
                'approved_by' => $uPincabAviari->id,
            ]);

            \App\Models\Reimbursement::create([
                'employee_id' => $empAoAviari->id,
                'title' => 'Klaim Tunjangan Kacamata Kerja',
                'category' => 'Kesehatan / Kacamata',
                'date' => Carbon::today()->subDays(5)->toDateString(),
                'amount' => 500000,
                'description' => 'Penggantian lensa kacamata resep dokter Optik Melawai Batam',
                'status' => 'Paid',
                'payment_date' => Carbon::today()->subDays(1)->toDateString(),
                'approved_by' => $uAdmin->id,
            ]);
        }

        // 11. Seed Sample Career Movements (Mutasi / Promosi)
        if ($empTellerBot) {
            \App\Models\CareerMovement::create([
                'employee_id' => $empTellerBot->id,
                'type' => 'Mutasi Cabang',
                'from_branch_id' => $kcBengkong->id,
                'to_branch_id' => $kcBotania->id,
                'from_position_id' => $posTeller->id,
                'to_position_id' => $posTeller->id,
                'effective_date' => '2026-01-01',
                'sk_number' => 'SK.DIR/BPR-BR/2026/01/015',
                'notes' => 'Rotasi penyegaran staf operasional kas ke KC Botania',
            ]);
        }

        // 12. Seed Sample Warning Letters (SP)
        $empTellerBengkong = Employee::where('email', 'teller.bengkong@bprbandaraya.com')->first();
        if ($empTellerBengkong) {
            \App\Models\WarningLetter::create([
                'employee_id' => $empTellerBengkong->id,
                'level' => 'Surat Peringatan 1 (SP 1)',
                'letter_number' => 'SP.01/SDM/BPR-BR/2026/007',
                'reason' => 'Keterlambatan presensi lebih dari 3 kali berturut-turut dalam 1 bulan tanpa surat izin resmi',
                'issued_date' => Carbon::today()->subMonths(1)->toDateString(),
                'expiry_date' => Carbon::today()->addMonths(5)->toDateString(),
                'status' => 'Aktif',
                'issued_by' => $uAdmin->id,
                'notes' => 'Telah dilakukan konseling pembinaan bersama Pincab Bengkong',
            ]);
        }
    }
}
