<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class JobApplicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'position_applied',
        'subject',
        'message',
        'cv_path',
        'cv_filename',
        'email_uid',
        'email_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'email_date' => 'datetime',
    ];

    /**
     * Promosikan / Terima Pelamar Kerja menjadi Pegawai Aktif (Employee)
     */
    public function promoteToEmployee(array $options = []): Employee
    {
        return DB::transaction(function () use ($options) {
            // 1. Tentukan Cabang Penempatan (default dari input atau KC Botania / Cabang pertama)
            $branchId = $options['branch_id'] ?? null;
            if (! $branchId) {
                $defaultBranch = Branch::where('name', 'LIKE', '%Botania%')->first() ?? Branch::first();
                $branchId = $defaultBranch?->id;
            }

            // 2. Tentukan Posisi & Departemen
            $positionId = $options['position_id'] ?? null;
            $departmentId = $options['department_id'] ?? null;

            if (! $positionId) {
                $pos = Position::where('name', 'LIKE', "%{$this->position_applied}%")->first()
                    ?? Position::first();
                $positionId = $pos?->id;
                $departmentId = $pos?->department_id;
            } elseif (! $departmentId) {
                $pos = Position::find($positionId);
                $departmentId = $pos?->department_id;
            }

            // 3. Generate NIP Unik Otomatis (BNK-YYYY-XXXX)
            $year = date('Y');
            $latestEmployee = Employee::where('nip', 'LIKE', "BNK-{$year}-%")->latest('id')->first();
            $nextSequence = 1;
            if ($latestEmployee && preg_match('/BNK-\d{4}-(\d+)/', $latestEmployee->nip, $matches)) {
                $nextSequence = ((int) $matches[1]) + 1;
            } else {
                $nextSequence = Employee::count() + 1;
            }
            $nip = $options['nip'] ?? sprintf("BNK-%s-%04d", $year, $nextSequence);

            // 4. Pastikan Akun User Login Terbuat dengan domain .com
            $officialEmail = $this->email;
            if (! str_ends_with(strtolower($officialEmail), '@bprbandaraya.com')) {
                $slug = Str::slug($this->name, '.');
                $candidateEmail = "{$slug}@bprbandaraya.com";
                if (User::where('email', $candidateEmail)->exists()) {
                    $candidateEmail = "{$slug}." . rand(10, 99) . "@bprbandaraya.com";
                }
                $officialEmail = $candidateEmail;
            }

            $user = User::where('email', $officialEmail)->first();
            if (! $user) {
                $user = User::create([
                    'name' => $this->name,
                    'email' => $officialEmail,
                    'password' => Hash::make('password'),
                    'role' => 'employee',
                    'branch_id' => $branchId,
                ]);
            }

            // 5. Buat atau Perbarui Data Employee
            $employee = Employee::where('email', $officialEmail)
                ->orWhere('nip', $nip)
                ->first();

            $joinDate = $options['join_date'] ?? now()->toDateString();
            $empStatus = $options['employment_status'] ?? 'Probation';

            if (! $employee) {
                $employee = Employee::create([
                    'user_id' => $user->id,
                    'nip' => $nip,
                    'full_name' => $this->name,
                    'email' => $officialEmail,
                    'phone' => $this->phone ?? '081200000000',
                    'gender' => 'Male',
                    'branch_id' => $branchId,
                    'department_id' => $departmentId,
                    'position_id' => $positionId,
                    'employment_status' => $empStatus,
                    'join_date' => $joinDate,
                    'status' => 'Active',
                ]);
            } else {
                $employee->update([
                    'user_id' => $user->id,
                    'status' => 'Active',
                    'branch_id' => $branchId,
                    'department_id' => $departmentId,
                    'position_id' => $positionId,
                ]);
            }

            // 6. Inisialisasi Saldo Cuti Awal 12 Hari
            $annualLeaveType = LeaveType::where('code', 'ANNUAL')->orWhere('name', 'LIKE', '%Tahunan%')->first();
            $leaveTypeId = $annualLeaveType ? $annualLeaveType->id : 1;

            EmployeeLeaveBalance::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'leave_type_id' => $leaveTypeId,
                    'year' => (int) date('Y'),
                ],
                [
                    'initial_quota' => 12,
                    'used_quota' => 0,
                    'remaining_quota' => 12,
                ]
            );

            // 7. Update status JobApplicant
            $this->update([
                'status' => 'Diterima',
                'notes' => ($this->notes ? $this->notes . "\n" : '') . "Resmi diangkat menjadi Pegawai Baru (NIP: {$nip}) pada tanggal " . date('d/m/Y') . ".",
            ]);

            return $employee;
        });
    }
}
