<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('training_name');
            $table->string('schedule')->nullable(); // e.g. "17-Jul-13", "17-24 Okt 2016"
            $table->string('organizer')->nullable(); // e.g. "BPR Banda Raya", "MAPPI", "DPD Perbarindo Kepri"
            $table->string('trainer')->nullable(); // Pemateri: e.g. "Bp. Kardi P, SH, M.Ba", "ESQ Team"
            $table->string('location')->nullable(); // Lokasi: "Batam", "Jakarta", "Batam - Zoom"
            $table->string('certificate_file')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_trainings');
    }
};
