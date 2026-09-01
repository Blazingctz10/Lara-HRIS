<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->unsignedTinyInteger('period_month'); // 1-12
            $table->unsignedSmallInteger('period_year'); // e.g. 2026
            
            // Manual Slip Components (MVP)
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('allowances', 15, 2)->default(0); // Tunjangan Jabatan, Transport, Cash Handling/Teller
            $table->decimal('deductions', 15, 2)->default(0); // Potongan BPJS, Pinjaman Karyawan
            $table->decimal('net_salary', 15, 2)->default(0);
            
            $table->date('payment_date')->nullable();
            $table->enum('status', ['Draft', 'Approved', 'Paid'])->default('Draft');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Compound Unique Index: 1 slip per employee per period
            $table->unique(['employee_id', 'period_month', 'period_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
