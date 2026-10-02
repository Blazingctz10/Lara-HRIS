<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Leave Types
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. ANNUAL, BLOCK_LEAVE, SICK, MATERNITY, SPECIAL
            $table->string('name');
            $table->unsignedInteger('default_days')->default(12);
            $table->boolean('is_block_leave')->default(false);
            $table->unsignedInteger('min_consecutive_days')->nullable(); // e.g. 5 for Block Leave OJK
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Employee Leave Balances (Leave Quotas per Year)
        Schema::create('employee_leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();
            $table->unsignedSmallInteger('year'); // e.g. 2026
            $table->unsignedInteger('initial_quota')->default(12);
            $table->unsignedInteger('used_quota')->default(0);
            $table->unsignedInteger('remaining_quota')->default(12);
            $table->timestamps();

            // Micro-adjustment #2: Compound Unique Index
            $table->unique(['employee_id', 'leave_type_id', 'year']);
        });

        // 3. Leave Requests
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('total_days');
            $table->text('reason');
            $table->string('attachment_url')->nullable();
            $table->string('status', 40)->default('Pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('employee_leave_balances');
        Schema::dropIfExists('leave_types');
    }
};
