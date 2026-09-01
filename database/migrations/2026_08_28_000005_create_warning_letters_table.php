<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warning_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('level', ['Teguran Lisan', 'Surat Peringatan 1 (SP 1)', 'Surat Peringatan 2 (SP 2)', 'Surat Peringatan 3 (SP 3)'])->default('Surat Peringatan 1 (SP 1)');
            $table->string('letter_number'); // e.g. "SP.01/SDM/BPR-BR/2026/012"
            $table->text('reason'); // Deskripsi pelanggaran SOP Kas / Kepatuhan
            $table->date('issued_date');
            $table->date('expiry_date')->nullable(); // Masa berlaku SP (misal: 6 bulan)
            $table->enum('status', ['Aktif', 'Kedaluwarsa', 'Dicabut / Pembinaan Berhasil'])->default('Aktif');
            $table->string('attachment_url')->nullable(); // Dokumen Berita Acara / SP
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warning_letters');
    }
};
