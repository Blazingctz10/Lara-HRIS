<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'ANNUAL', 'name' => 'Cuti Tahunan', 'default_days' => 12, 'is_block_leave' => false, 'max_consecutive_days' => 5, 'description' => 'Hak cuti tahunan reguler bagi karyawan aktif BPR Banda Raya.'],
            ['code' => 'SICK', 'name' => 'Cuti Sakit', 'default_days' => 14, 'is_block_leave' => false, 'description' => 'Izin sakit resmi dengan melampirkan surat keterangan dokter.'],
            ['code' => 'MATERNITY', 'name' => 'Cuti Melahirkan', 'default_days' => 90, 'is_block_leave' => false, 'max_consecutive_days' => 90, 'gender_restriction' => 'female', 'description' => 'Cuti melahirkan bagi staf wanita selama 3 bulan.'],
            ['code' => 'PATERNITY', 'name' => 'Cuti Mendampingi Istri Melahirkan', 'default_days' => 2, 'is_block_leave' => false, 'max_consecutive_days' => 2, 'gender_restriction' => 'male', 'description' => 'Hak cuti mendampingi persalinan bagi staf pria.'],
            ['code' => 'BEREAVEMENT', 'name' => 'Cuti Duka', 'default_days' => 3, 'is_block_leave' => false, 'max_consecutive_days' => 3, 'description' => 'Cuti kedukaan anggota keluarga inti.'],
            ['code' => 'LONG_LEAVE', 'name' => 'Cuti Besar', 'default_days' => 6, 'is_block_leave' => false, 'max_consecutive_days' => 6, 'description' => 'Cuti besar loyalitas bagi karyawan yang telah bekerja minimal 5 tahun (durasi 6 hari kerja).'],
            ['code' => 'BLOCK_LEAVE', 'name' => 'Block Leave (Wajib OJK)', 'default_days' => 5, 'is_block_leave' => true, 'min_consecutive_days' => 5, 'max_consecutive_days' => 5, 'description' => 'Kepatuhan regulasi perbankan OJK: Cuti berturut-turut minimal 5 hari kerja tanpa interupsi akses sistem.'],
        ];

        foreach ($types as $type) {
            LeaveType::updateOrCreate(['code' => $type['code']], $type);
        }
    }
}
