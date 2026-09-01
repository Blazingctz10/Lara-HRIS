<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reimbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('title'); // e.g. "Bensin Kunjungan Debitur Botania"
            $table->enum('category', ['Bensin & Transport AO', 'Survei Calon Debitur', 'Kesehatan / Kacamata', 'Konsumsi Lembur', 'Operasional Kas Cabang'])->default('Bensin & Transport AO');
            $table->date('date');
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->string('receipt_photo')->nullable(); // Foto struk/kwitansi
            $table->enum('status', ['Pending', 'Approved_Pincab', 'Paid', 'Rejected'])->default('Pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reimbursements');
    }
};
