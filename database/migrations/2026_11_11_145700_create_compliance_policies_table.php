<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('compliance_policies', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->string('title');
            $table->string('category')->index(); // apu_ppt, code_of_conduct, sop_operation, risk_management, company_regulation, other
            $table->text('summary')->nullable();
            $table->string('attachment_path');
            $table->string('file_size')->nullable();
            $table->date('effective_date');
            $table->string('target_audience')->default('all'); // all, branch_ops, executive_only
            $table->string('status')->default('active')->index(); // active, archived
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_policies');
    }
};
