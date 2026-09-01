<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->string('nip')->unique(); // e.g. BNK-2026-0001
            $table->string('nik', 20)->unique()->nullable(); // KTP
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->enum('gender', ['Male', 'Female'])->default('Male');
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->string('photo_url')->nullable();
            
            // Placement
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            
            // Employment status
            $table->enum('employment_status', ['Permanent', 'Contract', 'Probation', 'Internship'])->default('Contract');
            $table->date('join_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['Active', 'On Leave', 'Resigned', 'Terminated'])->default('Active');
            
            // Financial & Tax
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_holder')->nullable();
            $table->string('npwp', 30)->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
