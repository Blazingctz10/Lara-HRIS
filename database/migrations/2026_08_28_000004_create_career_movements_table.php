<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('career_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('type', ['Mutasi Cabang', 'Promosi Jabatan', 'Demosi', 'Rotasi Divisi', 'Pengangkatan Tetap'])->default('Mutasi Cabang');
            $table->foreignId('from_branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('to_branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('from_position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->foreignId('to_position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->date('effective_date');
            $table->string('sk_number')->nullable(); // e.g. "SK.DIR/BPR-BR/2026/08/042"
            $table->string('sk_attachment')->nullable(); // PDF SK
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_movements');
    }
};
