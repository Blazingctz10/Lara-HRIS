<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applicants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('position_applied')->default('Umum / Staf Operasional');
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('cv_filename')->nullable();
            $table->string('email_uid')->nullable()->index();
            $table->dateTime('email_date')->nullable();
            $table->enum('status', ['Baru', 'Screening', 'Interview', 'Diterima', 'Ditolak'])->default('Baru');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applicants');
    }
};
