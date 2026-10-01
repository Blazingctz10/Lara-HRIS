<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_performance_appraisals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->unsignedSmallInteger('period_year'); // Tahun Buku: e.g. 2024
            $table->string('letter_grade', 5)->default('B'); // A, B, C, D
            $table->decimal('score', 4, 2)->default(3.00); // Angka Mutu: 3.80, 3.77
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_performance_appraisals');
    }
};
