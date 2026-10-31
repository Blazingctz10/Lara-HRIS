<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Komponen Rincian Gaji & Tunjangan Resmi BPR
            $table->decimal('position_allowance', 15, 2)->default(0)->after('basic_salary'); // Tunjangan Jabatan
            $table->decimal('transport_allowance', 15, 2)->default(0)->after('position_allowance'); // Tunjangan Transport / Operasional
            $table->decimal('meal_allowance', 15, 2)->default(0)->after('transport_allowance'); // Uang Makan
            $table->decimal('overtime_pay', 15, 2)->default(0)->after('meal_allowance'); // Total Uang Lembur
            $table->decimal('reimbursement_pay', 15, 2)->default(0)->after('overtime_pay'); // Total Klaim Reimbursement

            // Komponen Potongan Terinci
            $table->decimal('bpjs_tk_deduction', 15, 2)->default(0)->after('deductions'); // Potongan BPJS Ketenagakerjaan (JHT + JP)
            $table->decimal('bpjs_kes_deduction', 15, 2)->default(0)->after('bpjs_tk_deduction'); // Potongan BPJS Kesehatan
            $table->decimal('pph21_deduction', 15, 2)->default(0)->after('bpjs_kes_deduction'); // Pajak PPh 21
            $table->decimal('late_penalty_deduction', 15, 2)->default(0)->after('pph21_deduction'); // Potongan Denda Telat Presensi
            $table->decimal('loan_deduction', 15, 2)->default(0)->after('late_penalty_deduction'); // Potongan Kasbon / Pinjaman Pegawai

            // Data Pelengkap Kalkulasi
            $table->unsignedSmallInteger('overtime_hours')->default(0)->after('loan_deduction'); // Jumlah jam lembur periode ini
            $table->unsignedSmallInteger('late_count')->default(0)->after('overtime_hours'); // Jumlah hari telat periode ini
            $table->string('ptkp_status_used', 10)->nullable()->after('late_count'); // Status PTKP saat perhitungan
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'position_allowance',
                'transport_allowance',
                'meal_allowance',
                'overtime_pay',
                'reimbursement_pay',
                'bpjs_tk_deduction',
                'bpjs_kes_deduction',
                'pph21_deduction',
                'late_penalty_deduction',
                'loan_deduction',
                'overtime_hours',
                'late_count',
                'ptkp_status_used',
            ]);
        });
    }
};
