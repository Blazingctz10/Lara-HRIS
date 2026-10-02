<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeFamily;
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
     * Menghasilkan isi template CSV untuk impor data master pegawai lengkap sesuai spesifikasi BPR
     */
    public function generateEmployeeTemplateCsv(): string
    {
        $headers = [
            'NIP',
            'NIK',
            'Nama_Lengkap',
            'Email_Kantor',
            'Email_Pribadi',
            'Nomor_Telepon',
            'Jenis_Kelamin',
            'Tempat_Lahir',
            'Tanggal_Lahir',
            'Alamat',
            'Kantor_Cabang',
            'Divisi',
            'Jabatan',
            'Tanggal_Efektif_Jabatan',
            'Grade',
            'Tanggal_Efektif_Grade',
            'Status_Kerja',
            'Tanggal_Masuk',
            'Tanggal_Pengangkatan_Tetap',
            'Akhir_Kontrak',
            'Status_Pegawai',
            'Jenjang_Pendidikan',
            'Jurusan',
            'Universitas',
            'IPK',
            'NPWP',
            'No_BPJSTK',
            'No_BPJS_Kes',
            'Rekening_BPR',
            'Nama_Bank_Lain',
            'Nomor_Rekening_Bank_Lain',
            'Atas_Nama_Rekening',
            'Status_PTKP',
            'Kontak_Darurat_Nama',
            'Kontak_Darurat_Nomor',
            'Kontak_Darurat_Hubungan',
        ];

        $sampleRows = [
            [
                'BPR-2026-0101',
                '2171012304950001',
                'Hendri Prasetyo',
                'hendri.prasetyo@bprbandaraya.com',
                'hendri.pribadi@gmail.com',
                '081266554411',
                'Male',
                'Batam',
                '1995-04-23',
                'Jl. Raja Ali Haji No. 12, Batam',
                'Kantor Cabang Botania',
                'Operasional & Pelayanan',
                'Customer Service',
                '2024-01-15',
                '4',
                '2024-01-15',
                'Permanent',
                '2024-01-15',
                '2025-01-15',
                '',
                'Active',
                'S-1',
                'Manajemen Perbankan',
                'Universitas Internasional Batam',
                '3.45',
                '81.234.567.8-215.000',
                '08017700462',
                '0001617375611',
                '01.10.05307',
                'Bank Mandiri',
                '109-00-1514578-2',
                'Hendri Prasetyo',
                'K/1',
                'Dewi Anggraini',
                '081299887711',
                'Istri',
            ],
            [
                'BPR-2026-0102',
                '2171025508980002',
                'Siti Rahmadani',
                'siti.rahmadani@bprbandaraya.com',
                'siti.rahmadani@gmail.com',
                '081377889922',
                'Female',
                'Padang',
                '1998-08-15',
                'Komplek Aviari Blok B No. 5, Batu Aji',
                'Kantor Cabang Aviari',
                'Kredit & Pemasaran',
                'Account Officer (AO)',
                '2024-06-01',
                '5',
                '2024-06-01',
                'Contract',
                '2024-06-01',
                '',
                '2027-06-01',
                'Active',
                'D-3',
                'Akuntansi',
                'Politeknik Negeri Padang',
                '3.60',
                '82.345.678.9-215.000',
                '08017700499',
                '0001617375699',
                '01.10.05308',
                'BCA',
                '8210349911',
                'Siti Rahmadani',
                'TK/0',
                'Hj. Maryam',
                '081366551122',
                'Ibu',
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
     * Eksekusi impor pegawai dari berkas CSV dengan dukungan seluruh atribut profil BPR
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
                $fullName = trim($data['nama_lengkap'] ?? ($data['nama'] ?? ($data['full_name'] ?? '')));

                if (empty($nip) || empty($fullName)) {
                    $errors[] = "Baris " . ($index + 2) . ": NIP dan Nama Lengkap wajib diisi.";
                    continue;
                }

                // 1. Tangani Email & Domain (.com)
                $email = trim($data['email_kantor'] ?? ($data['email'] ?? ($data['email_dinas'] ?? '')));
                if (empty($email)) {
                    $cleanNip = Str::slug($nip);
                    $email = "pegawai.{$cleanNip}@bprbandaraya.com";
                }
                // Pastikan domain .com
                $email = str_ireplace('@bprbandaraya.co.id', '@bprbandaraya.com', $email);

                // 2. Hubungkan / Buat Kantor Cabang
                $branchName = trim($data['kantor_cabang'] ?? ($data['cabang'] ?? ($data['branch'] ?? 'Kantor Cabang Botania')));
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
                $deptName = trim($data['divisi'] ?? ($data['departemen'] ?? ($data['department'] ?? 'Operasional & Pelayanan')));
                $dept = Department::where('name', 'LIKE', "%{$deptName}%")->first();
                if (! $dept) {
                    $dept = Department::create([
                        'name' => $deptName,
                        'code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $deptName), 0, 4)) . rand(10, 99),
                    ]);
                }

                // 4. Hubungkan / Buat Jabatan
                $posName = trim($data['jabatan'] ?? ($data['posisi'] ?? ($data['position'] ?? 'Staf Operasional')));
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
                $employmentStatus = ucfirst(strtolower(trim($data['status_kerja'] ?? ($data['employment_status'] ?? 'Permanent'))));
                if (! in_array($employmentStatus, ['Permanent', 'Contract', 'Probation', 'Internship'])) {
                    $employmentStatus = 'Permanent';
                }

                $gender = ucfirst(strtolower(trim($data['jenis_kelamin'] ?? ($data['gender'] ?? 'Male'))));
                if (! in_array($gender, ['Male', 'Female'])) {
                    $gender = (str_contains(strtolower($gender), 'wanita') || str_contains(strtolower($gender), 'perempuan') || str_contains(strtolower($gender), 'p')) ? 'Female' : 'Male';
                }

                $birthDate = ! empty($data['tanggal_lahir']) ? Carbon::parse($data['tanggal_lahir'])->toDateString() : null;
                $joinDate = ! empty($data['tanggal_masuk']) ? Carbon::parse($data['tanggal_masuk'])->toDateString() : now()->toDateString();
                $jobEffectiveDate = ! empty($data['tanggal_efektif_jabatan']) ? Carbon::parse($data['tanggal_efektif_jabatan'])->toDateString() : $joinDate;
                $gradeEffectiveDate = ! empty($data['tanggal_efektif_grade']) ? Carbon::parse($data['tanggal_efektif_grade'])->toDateString() : $joinDate;
                $permanentDate = ! empty($data['tanggal_pengangkatan_tetap']) ? Carbon::parse($data['tanggal_pengangkatan_tetap'])->toDateString() : null;
                $contractEndDate = ! empty($data['akhir_kontrak']) ? Carbon::parse($data['akhir_kontrak'])->toDateString() : null;

                $status = trim($data['status_pegawai'] ?? ($data['status'] ?? 'Active'));
                if (! in_array($status, ['Active', 'On Leave', 'Resigned', 'Terminated'])) {
                    $status = 'Active';
                }

                // 7. Simpan atau Perbarui Profil Employee
                $employee = Employee::where('nip', $nip)->first();
                $isNew = ! $employee;

                if ($isNew) {
                    $employee = new Employee();
                    $employee->nip = $nip;
                }

                $externalBankAcc = $data['nomor_rekening_bank_lain'] ?? ($data['nomor_rekening'] ?? ($data['bank_external_account'] ?? null));
                $bprBankAcc = $data['rekening_bpr'] ?? ($data['rek_bpr'] ?? ($data['bank_bpr_account'] ?? null));

                $employee->fill([
                    'user_id' => $user->id,
                    'nik' => $data['nik'] ?? null,
                    'full_name' => $fullName,
                    'email' => $email,
                    'personal_email' => $data['email_pribadi'] ?? ($data['personal_email'] ?? null),
                    'phone' => $data['nomor_telepon'] ?? ($data['telepon'] ?? ($data['phone'] ?? '081200000000')),
                    'gender' => $gender,
                    'birth_date' => $birthDate,
                    'birth_place' => $data['tempat_lahir'] ?? ($data['birth_place'] ?? null),
                    'address' => $data['alamat'] ?? ($data['address'] ?? 'Batam, Kepulauan Riau'),
                    'branch_id' => $branch->id,
                    'department_id' => $dept->id,
                    'position_id' => $position->id,
                    'job_effective_date' => $jobEffectiveDate,
                    'grade' => ! empty($data['grade']) ? (int) $data['grade'] : null,
                    'grade_effective_date' => $gradeEffectiveDate,
                    'employment_status' => $employmentStatus,
                    'join_date' => $joinDate,
                    'permanent_date' => $permanentDate,
                    'contract_end_date' => $contractEndDate,
                    'status' => $status,
                    'education_level' => $data['jenjang_pendidikan'] ?? ($data['education_level'] ?? null),
                    'education_major' => $data['jurusan'] ?? ($data['education_major'] ?? null),
                    'education_institution' => $data['universitas'] ?? ($data['sekolah'] ?? ($data['education_institution'] ?? null)),
                    'ipk' => ! empty($data['ipk']) ? (float) $data['ipk'] : null,
                    'bank_account_number' => $externalBankAcc ?: $bprBankAcc,
                    'bank_account_holder' => $data['atas_nama_rekening'] ?? ($data['bank_account_holder'] ?? $fullName),
                    'bank_bpr_account' => $bprBankAcc,
                    'bank_external_name' => $data['nama_bank_lain'] ?? ($data['bank_external_name'] ?? null),
                    'bank_external_account' => $externalBankAcc,
                    'npwp' => $data['npwp'] ?? null,
                    'bpjs_tk_number' => $data['no_bpjstk'] ?? ($data['bpjstk'] ?? ($data['bpjs_tk_number'] ?? null)),
                    'bpjs_kes_number' => $data['no_bpjs_kes'] ?? ($data['bpjs_kes'] ?? ($data['bpjs_kes_number'] ?? null)),
                    'ptkp_status' => $data['status_ptkp'] ?? ($data['ptkp'] ?? ($data['ptkp_status'] ?? null)),
                    'emergency_contact_name' => $data['kontak_darurat_nama'] ?? ($data['emergency_contact_name'] ?? null),
                    'emergency_contact_phone' => $data['kontak_darurat_nomor'] ?? ($data['emergency_contact_phone'] ?? null),
                    'emergency_contact_relation' => $data['kontak_darurat_hubungan'] ?? ($data['emergency_contact_relation'] ?? null),
                ]);
                $employee->save();

                // 8. Tambahkan data keluarga otomatis jika kontak darurat diisi
                if (! empty($employee->emergency_contact_name)) {
                    EmployeeFamily::firstOrCreate(
                        [
                            'employee_id' => $employee->id,
                            'name' => $employee->emergency_contact_name,
                        ],
                        [
                            'relationship' => $employee->emergency_contact_relation ?: 'Keluarga',
                            'birth_place' => $employee->birth_place ?: 'Batam',
                            'gender' => in_array($employee->emergency_contact_relation, ['Istri', 'Ibu']) ? 'Perempuan' : 'Laki-laki',
                        ]
                    );
                }

                // 9. Inisialisasi Saldo Cuti Pegawai jika belum ada
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
                if (count($row) < count($header)) {
                    $errors[] = "Baris " . ($index + 2) . ": Format kolom tidak sesuai.";
                    continue;
                }

                $data = array_combine(array_slice($header, 0, count($row)), $row);

                $nip = trim($data['nip'] ?? '');
                $date = trim($data['tanggal'] ?? ($data['date'] ?? ''));

                if (empty($nip) || empty($date)) {
                    $errors[] = "Baris " . ($index + 2) . ": NIP dan Tanggal wajib diisi.";
                    continue;
                }

                $employee = Employee::where('nip', $nip)->first();
                if (! $employee) {
                    $errors[] = "Baris " . ($index + 2) . ": Pegawai dengan NIP {$nip} tidak terdaftar di sistem.";
                    continue;
                }

                $clockIn = ! empty($data['jam_masuk']) ? Carbon::parse("{$date} " . $data['jam_masuk']) : Carbon::parse("{$date} 08:00:00");
                $clockOut = ! empty($data['jam_keluar']) ? Carbon::parse("{$date} " . $data['jam_keluar']) : null;
                $status = ucfirst(strtolower(trim($data['status'] ?? 'Present')));
                if (! in_array($status, ['Present', 'Late', 'Absent', 'Leave', 'Holiday'])) {
                    $status = 'Present';
                }

                $notes = trim($data['catatan'] ?? 'Impor Rekapan Fingerprint');

                Attendance::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'date' => Carbon::parse($date)->toDateString(),
                    ],
                    [
                        'branch_id' => $employee->branch_id,
                        'clock_in' => $clockIn,
                        'clock_out' => $clockOut,
                        'status' => $status,
                        'notes' => $notes,
                        'lat_in' => $employee->branch?->latitude ?? 1.1185,
                        'long_in' => $employee->branch?->longitude ?? 104.0532,
                        'distance_in_meters' => 5,
                    ]
                );

                $imported++;
            }

            DB::commit();

            return [
                'success' => true,
                'message' => "Impor log kehadiran sukses: {$imported} catatan presensi tersimpan/diperbarui.",
                'imported' => $imported,
                'errors' => $errors,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Gagal memproses log absensi: ' . $e->getMessage(),
                'imported' => 0,
                'errors' => [$e->getMessage()],
            ];
        }
    }
}
