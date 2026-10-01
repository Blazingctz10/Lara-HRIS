<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DataMigrationService
{
    /**
     * Menghasilkan isi template CSV untuk impor data master pegawai
     */
    public function generateEmployeeTemplateCsv(): string
    {
        $headers = [
            'NIP',
            'NIK',
            'Nama_Lengkap',
            'Email',
            'Nomor_Telepon',
            'Jenis_Kelamin',
            'Tanggal_Lahir',
            'Alamat',
            'Kantor_Cabang',
            'Divisi',
            'Jabatan',
            'Grade',
            'Status_Kerja',
            'Tanggal_Masuk',
            'Tempat_Lahir',
            'Jenjang_Pendidikan',
            'Jurusan',
            'Universitas',
            'IPK',
            'Email_Pribadi',
            'Nomor_Rekening',
            'Atas_Nama_Rekening',
            'Rekening_BPR',
            'NPWP',
            'No_BPJSTK',
            'No_BPJS_Kes',
            'Status_PTKP',
        ];

        $sampleRows = [
            [
                'BPR-2026-0101',
                '2171012304950001',
                'Hendri Prasetyo',
                'hendri.prasetyo@bprbandaraya.com',
                '081266554411',
                'Male',
                '1995-04-23',
                'Jl. Raja Ali Haji No. 12, Batam',
                'Kantor Cabang Botania',
                'Operasional & Pelayanan',
                'Customer Service',
                '4',
                'Permanent',
                '2024-01-15',
                'Batam',
                'S-1',
                'Manajemen Perbankan',
                'Universitas Internasional Batam',
                '3.45',
                'hendri.pribadi@gmail.com',
                '1102938475',
                'Hendri Prasetyo',
                '01.10.05307',
                '81.234.567.8-215.000',
                '08017700462',
                '0001617375611',
                'K/1',
            ],
            [
                'BPR-2026-0102',
                '2171025508980002',
                'Siti Rahmadani',
                'siti.rahmadani@bprbandaraya.com',
                '081377889922',
                'Female',
                '1998-08-15',
                'Komplek Aviari Blok B No. 5, Batu Aji',
                'Kantor Cabang Aviari',
                'Kredit & Pemasaran',
                'Account Officer',
                '5',
                'Contract',
                '2024-06-01',
                'Padang',
                'D-3',
                'Akuntansi',
                'Politeknik Negeri Padang',
                '3.60',
                'siti.rahmadani@gmail.com',
                '1102938499',
                'Siti Rahmadani',
                '01.10.05308',
                '82.345.678.9-215.000',
                '08017700499',
                '0001617375699',
                'TK/0',
            ],
        ];

        $output = fopen('php://temp', 'r+');
        fputcsv($output, $headers);
        foreach ($sampleRows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Menghasilkan isi template CSV untuk impor data absensi / fingerprint
     */
    public function generateAttendanceTemplateCsv(): string
    {
        $headers = [
            'NIP',
            'Tanggal',
            'Jam_Masuk',
            'Jam_Keluar',
            'Status',
            'Catatan',
        ];

        $sampleRows = [
            ['BPR-2026-0101', '2026-10-01', '07:55:00', '17:05:00', 'Present', 'Log mesin fingerprint utama'],
            ['BPR-2026-0102', '2026-10-01', '08:14:00', '17:00:00', 'Late', 'Keterlambatan 14 menit'],
        ];

        $output = fopen('php://temp', 'r+');
        fputcsv($output, $headers);
        foreach ($sampleRows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Eksekusi impor pegawai dari berkas CSV
     */
    public function importEmployeesFromCsv(string $filePath): array
    {
        if (! file_exists($filePath) || ! is_readable($filePath)) {
            return [
                'success' => false,
                'message' => 'Berkas CSV tidak ditemukan atau tidak dapat dibaca.',
                'imported' => 0,
                'updated' => 0,
                'errors' => [],
            ];
        }

        $content = file_get_contents($filePath);
        // Hilangkan UTF-8 BOM jika ada
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        $lines = preg_split('/\r\n|\r|\n/', trim($content));

        if (count($lines) < 2) {
            return [
                'success' => false,
                'message' => 'Berkas CSV kosong atau tidak memiliki baris data.',
                'imported' => 0,
                'updated' => 0,
                'errors' => [],
            ];
        }

        // Deteksi pemisah delimiter (koma atau titik koma)
        $delimiter = strpos($lines[0], ';') !== false && strpos($lines[0], ',') === false ? ';' : ',';

        $header = str_getcsv(array_shift($lines), $delimiter);
        $header = array_map(fn ($h) => trim(strtolower(str_replace([' ', '-'], '_', $h))), $header);

        $imported = 0;
        $updated = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($lines as $index => $line) {
                if (empty(trim($line))) {
                    continue;
                }

                $row = str_getcsv($line, $delimiter);
                if (count($row) < count($header)) {
                    $errors[] = "Baris " . ($index + 2) . ": Jumlah kolom tidak sesuai dengan header.";
                    continue;
                }

                $data = array_combine(array_slice($header, 0, count($row)), $row);

                $nip = trim($data['nip'] ?? '');
                $fullName = trim($data['nama_lengkap'] ?? ($data['nama'] ?? ''));

                if (empty($nip) || empty($fullName)) {
                    $errors[] = "Baris " . ($index + 2) . ": NIP dan Nama Lengkap wajib diisi.";
                    continue;
                }

                // 1. Tangani Email & Domain (.com)
                $email = trim($data['email'] ?? '');
                if (empty($email)) {
                    $cleanNip = Str::slug($nip);
                    $email = "pegawai.{$cleanNip}@bprbandaraya.com";
                }
                // Pastikan domain .com
                $email = str_ireplace('@bprbandaraya.co.id', '@bprbandaraya.com', $email);

                // 2. Hubungkan / Buat Kantor Cabang
                $branchName = trim($data['kantor_cabang'] ?? ($data['cabang'] ?? 'Kantor Cabang Botania'));
                $branch = Branch::where('name', 'LIKE', "%{$branchName}%")->first();
                if (! $branch) {
                    $branch = Branch::create([
                        'name' => $branchName,
                        'code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $branchName), 0, 5)) . rand(10, 99),
                        'city' => 'Batam',
                        'address' => 'Batam, Kepulauan Riau',
                        'latitude' => 1.1185,
                        'longitude' => 104.0532,
                        'radius_meters' => 100,
                    ]);
                }

                // 3. Hubungkan / Buat Divisi / Departemen
                $deptName = trim($data['divisi'] ?? ($data['departemen'] ?? 'Operasional & Pelayanan'));
                $dept = Department::where('name', 'LIKE', "%{$deptName}%")->first();
                if (! $dept) {
                    $dept = Department::create([
                        'name' => $deptName,
                        'code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $deptName), 0, 4)) . rand(10, 99),
                    ]);
                }

                // 4. Hubungkan / Buat Jabatan
                $posName = trim($data['jabatan'] ?? ($data['posisi'] ?? 'Staf Operasional'));
                $position = Position::where('name', 'LIKE', "%{$posName}%")->first();
                if (! $position) {
                    $position = Position::create([
                        'department_id' => $dept->id,
                        'name' => $posName,
                        'grade_level' => 'Staff',
                    ]);
                }

                // 5. Buat / Perbarui Akun User
                $user = User::where('email', $email)->first();
                if (! $user) {
                    $user = User::create([
                        'name' => $fullName,
                        'email' => $email,
                        'password' => Hash::make('password'),
                        'role' => 'employee',
                        'branch_id' => $branch->id,
                    ]);
                }

                // 6. Tangani Status Kerja & Format Tanggal
                $employmentStatus = ucfirst(strtolower(trim($data['status_kerja'] ?? 'Permanent')));
                if (! in_array($employmentStatus, ['Permanent', 'Contract', 'Probation', 'Internship'])) {
                    $employmentStatus = 'Permanent';
                }

                $gender = ucfirst(strtolower(trim($data['jenis_kelamin'] ?? 'Male')));
                if (! in_array($gender, ['Male', 'Female'])) {
                    $gender = (str_contains(strtolower($gender), 'wanita') || str_contains(strtolower($gender), 'perempuan') || str_contains(strtolower($gender), 'p')) ? 'Female' : 'Male';
                }

                $birthDate = ! empty($data['tanggal_lahir']) ? Carbon::parse($data['tanggal_lahir'])->toDateString() : null;
                $joinDate = ! empty($data['tanggal_masuk']) ? Carbon::parse($data['tanggal_masuk'])->toDateString() : now()->toDateString();

                // 7. Simpan atau Perbarui Profil Employee
                $employee = Employee::where('nip', $nip)->first();
                $isNew = ! $employee;

                if ($isNew) {
                    $employee = new Employee();
                    $employee->nip = $nip;
                }

                $employee->fill([
                    'user_id' => $user->id,
                    'nik' => $data['nik'] ?? null,
                    'full_name' => $fullName,
                    'email' => $email,
                    'personal_email' => $data['email_pribadi'] ?? null,
                    'phone' => $data['nomor_telepon'] ?? ($data['telepon'] ?? '081200000000'),
                    'gender' => $gender,
                    'birth_date' => $birthDate,
                    'birth_place' => $data['tempat_lahir'] ?? null,
                    'address' => $data['alamat'] ?? 'Batam, Kepulauan Riau',
                    'branch_id' => $branch->id,
                    'department_id' => $dept->id,
                    'position_id' => $position->id,
                    'grade' => ! empty($data['grade']) ? (int) $data['grade'] : null,
                    'employment_status' => $employmentStatus,
                    'join_date' => $joinDate,
                    'status' => 'Active',
                    'education_level' => $data['jenjang_pendidikan'] ?? null,
                    'education_major' => $data['jurusan'] ?? null,
                    'education_institution' => $data['universitas'] ?? ($data['sekolah'] ?? null),
                    'ipk' => ! empty($data['ipk']) ? (float) $data['ipk'] : null,
                    'bank_account_number' => $data['nomor_rekening'] ?? null,
                    'bank_account_holder' => $data['atas_nama_rekening'] ?? $fullName,
                    'bank_bpr_account' => $data['rekening_bpr'] ?? ($data['rek_bpr'] ?? null),
                    'npwp' => $data['npwp'] ?? null,
                    'bpjs_tk_number' => $data['no_bpjstk'] ?? ($data['bpjstk'] ?? null),
                    'bpjs_kes_number' => $data['no_bpjs_kes'] ?? ($data['bpjs_kes'] ?? null),
                    'ptkp_status' => $data['status_ptkp'] ?? ($data['ptkp'] ?? null),
                ]);
                $employee->save();

                // 8. Inisialisasi Saldo Cuti Pegawai jika belum ada
                $annualLeaveType = \App\Models\LeaveType::where('code', 'ANNUAL')->orWhere('name', 'LIKE', '%Tahunan%')->first();
                $leaveTypeId = $annualLeaveType ? $annualLeaveType->id : 1;

                $balance = EmployeeLeaveBalance::where('employee_id', $employee->id)
                    ->where('leave_type_id', $leaveTypeId)
                    ->where('year', (int) date('Y'))
                    ->first();

                if (! $balance) {
                    EmployeeLeaveBalance::create([
                        'employee_id' => $employee->id,
                        'leave_type_id' => $leaveTypeId,
                        'year' => (int) date('Y'),
                        'initial_quota' => 12,
                        'used_quota' => 0,
                        'remaining_quota' => 12,
                    ]);
                }

                if ($isNew) {
                    $imported++;
                } else {
                    $updated++;
                }
            }

            DB::commit();

            return [
                'success' => true,
                'message' => "Proses impor selesai: {$imported} pegawai baru ditambahkan, {$updated} pegawai diperbarui.",
                'imported' => $imported,
                'updated' => $updated,
                'errors' => $errors,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Gagal memproses berkas CSV: ' . $e->getMessage(),
                'imported' => 0,
                'updated' => 0,
                'errors' => [$e->getMessage()],
            ];
        }
    }

    /**
     * Eksekusi impor log presensi dari berkas CSV
     */
    public function importAttendancesFromCsv(string $filePath): array
    {
        if (! file_exists($filePath) || ! is_readable($filePath)) {
            return [
                'success' => false,
                'message' => 'Berkas CSV tidak ditemukan.',
                'imported' => 0,
                'errors' => [],
            ];
        }

        $content = file_get_contents($filePath);
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        $lines = preg_split('/\r\n|\r|\n/', trim($content));

        if (count($lines) < 2) {
            return [
                'success' => false,
                'message' => 'Berkas CSV tidak memiliki data.',
                'imported' => 0,
                'errors' => [],
            ];
        }

        $delimiter = strpos($lines[0], ';') !== false && strpos($lines[0], ',') === false ? ';' : ',';
        $header = str_getcsv(array_shift($lines), $delimiter);
        $header = array_map(fn ($h) => trim(strtolower(str_replace([' ', '-'], '_', $h))), $header);

        $imported = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($lines as $index => $line) {
                if (empty(trim($line))) {
                    continue;
                }

                $row = str_getcsv($line, $delimiter);
                $data = array_combine(array_slice($header, 0, count($row)), $row);

                $nip = trim($data['nip'] ?? '');
                $date = trim($data['tanggal'] ?? '');

                if (empty($nip) || empty($date)) {
                    $errors[] = "Baris " . ($index + 2) . ": NIP dan Tanggal wajib diisi.";
                    continue;
                }

                $employee = Employee::where('nip', $nip)->first();
                if (! $employee) {
                    $errors[] = "Baris " . ($index + 2) . ": Pegawai dengan NIP '{$nip}' tidak ditemukan.";
                    continue;
                }

                $clockIn = ! empty($data['jam_masuk']) ? trim($data['jam_masuk']) : null;
                $clockOut = ! empty($data['jam_keluar']) ? trim($data['jam_keluar']) : null;
                $status = trim($data['status'] ?? 'Present');

                // Cocokkan status enum
                if (! in_array($status, ['Present', 'Late', 'Absent', 'On_Leave'])) {
                    $status = 'Present';
                }

                Attendance::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'date' => Carbon::parse($date)->toDateString(),
                    ],
                    [
                        'clock_in' => $clockIn,
                        'clock_out' => $clockOut,
                        'status' => $status,
                        'notes' => $data['catatan'] ?? 'Impor dari file log absensi sistem/mesin lama',
                        'distance_in_meters' => 0,
                    ]
                );

                $imported++;
            }

            DB::commit();

            return [
                'success' => true,
                'message' => "Berhasil mengimpor/memperbarui {$imported} rekaman absensi.",
                'imported' => $imported,
                'errors' => $errors,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Gagal mengimpor absensi: ' . $e->getMessage(),
                'imported' => 0,
                'errors' => [$e->getMessage()],
            ];
        }
    }
}
