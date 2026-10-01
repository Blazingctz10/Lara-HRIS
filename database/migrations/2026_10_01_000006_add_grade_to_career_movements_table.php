<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('career_movements', function (Blueprint $table) {
            $table->unsignedTinyInteger('grade')->nullable()->after('to_position_id');
            $table->date('grade_effective_date')->nullable()->after('grade');
            $table->string('movement_notes')->nullable()->after('grade_effective_date'); // Karyawan Kontrak, Karyawan Tetap, Kenaikan Grade, dll
        });
    }

    public function down(): void
    {
        Schema::table('career_movements', function (Blueprint $table) {
            $table->dropColumn(['grade', 'grade_effective_date', 'movement_notes']);
        });
    }
};
