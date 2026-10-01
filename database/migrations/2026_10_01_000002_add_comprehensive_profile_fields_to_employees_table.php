<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Data Kepegawaian & Grade BPR
            $table->date('job_effective_date')->nullable()->after('position_id');
            $table->unsignedTinyInteger('grade')->nullable()->after('job_effective_date'); // Grade 1 s/d 10
            $table->date('grade_effective_date')->nullable()->after('grade');
            $table->date('permanent_date')->nullable()->after('join_date');
            $table->date('exit_date')->nullable()->after('end_date');
            $table->string('exit_reason')->nullable()->after('exit_date'); // Mengundurkan diri, Habis kontrak, Diberhentikan, Usia Pensiun

            // Data Pribadi & Pendidikan
            $table->string('birth_place')->nullable()->after('birth_date');
            $table->string('education_level', 20)->nullable()->after('address'); // SMP, SMA, SMK, D-1, D-3, D-4, S-1, S-2
            $table->string('education_major')->nullable()->after('education_level');
            $table->string('education_institution')->nullable()->after('education_major');
            $table->decimal('ipk', 4, 2)->nullable()->after('education_institution');
            $table->string('personal_email')->nullable()->after('email');

            // Legalitas BPJS & Perbankan BPR
            $table->string('bpjs_tk_number', 40)->nullable()->after('npwp');
            $table->string('bpjs_kes_number', 40)->nullable()->after('bpjs_tk_number');
            $table->string('bank_bpr_account', 40)->nullable()->after('bpjs_kes_number');
            $table->string('bank_external_name', 50)->nullable()->after('bank_bpr_account');
            $table->string('bank_external_account', 50)->nullable()->after('bank_external_name');

            // Pajak PTKP & Kontak Darurat
            $table->string('ptkp_status', 10)->nullable()->after('bank_external_account'); // TK/0, TK/1, ..., K/3
            $table->string('emergency_contact_name')->nullable()->after('ptkp_status');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relation')->nullable()->after('emergency_contact_phone');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'job_effective_date',
                'grade',
                'grade_effective_date',
                'permanent_date',
                'exit_date',
                'exit_reason',
                'birth_place',
                'education_level',
                'education_major',
                'education_institution',
                'ipk',
                'personal_email',
                'bpjs_tk_number',
                'bpjs_kes_number',
                'bank_bpr_account',
                'bank_external_name',
                'bank_external_account',
                'ptkp_status',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relation',
            ]);
        });
    }
};
