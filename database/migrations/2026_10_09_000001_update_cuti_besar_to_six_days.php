<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('leave_types')
            ->where('code', 'LONG_LEAVE')
            ->update([
                'default_days' => 6,
                'max_consecutive_days' => 6,
                'description' => 'Cuti besar loyalitas bagi pegawai yang telah bekerja minimal 5 tahun (durasi 6 hari kerja).',
            ]);
    }

    public function down(): void
    {
        DB::table('leave_types')
            ->where('code', 'LONG_LEAVE')
            ->update([
                'default_days' => 30,
                'max_consecutive_days' => 30,
            ]);
    }
};
