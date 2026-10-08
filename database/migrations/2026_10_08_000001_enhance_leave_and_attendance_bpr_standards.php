<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. leave_types
        if (! Schema::hasColumn('leave_types', 'max_consecutive_days')) {
            Schema::table('leave_types', function (Blueprint $table) {
                $table->unsignedInteger('max_consecutive_days')->nullable();
            });
        }
        if (! Schema::hasColumn('leave_types', 'gender_restriction')) {
            Schema::table('leave_types', function (Blueprint $table) {
                $table->string('gender_restriction', 10)->nullable();
            });
        }

        // 2. leave_requests (Add columns safely)
        $leaveReqCols = [
            'leave_address' => 'TEXT',
            'contact_phone' => 'TEXT',
            'approved_hrd_by' => 'INTEGER',
            'approved_komisaris_by' => 'INTEGER',
            'komisaris_notes' => 'TEXT',
        ];
        foreach ($leaveReqCols as $col => $type) {
            if (! Schema::hasColumn('leave_requests', $col)) {
                DB::statement("ALTER TABLE leave_requests ADD COLUMN {$col} {$type} NULL");
            }
        }

        // 3. attendances (Add late minutes & penalty fee)
        if (! Schema::hasColumn('attendances', 'late_minutes')) {
            DB::statement("ALTER TABLE attendances ADD COLUMN late_minutes INTEGER NOT NULL DEFAULT 0");
        }
        if (! Schema::hasColumn('attendances', 'penalty_amount')) {
            DB::statement("ALTER TABLE attendances ADD COLUMN penalty_amount NUMERIC NOT NULL DEFAULT 0");
        }

        // 4. attendance_dispensations (Logbook HRD & GA)
        if (! Schema::hasTable('attendance_dispensations')) {
            Schema::create('attendance_dispensations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->date('date');
                $table->string('dispensation_type', 50)->default('Izin via Chat/WA');
                $table->string('custom_reason')->nullable(); // Opsi isi sendiri bebas sesuai permintaan user
                $table->text('description')->nullable();
                $table->string('reference_contact', 50)->nullable();
                $table->string('attachment')->nullable();
                $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['employee_id', 'date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_dispensations');
    }
};
