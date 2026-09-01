<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('date');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->enum('status', ['Present', 'Late', 'Excused', 'Absent'])->default('Present');
            
            // Geolocation & Selfie
            $table->decimal('lat_in', 10, 8)->nullable();
            $table->decimal('long_in', 11, 8)->nullable();
            $table->decimal('lat_out', 10, 8)->nullable();
            $table->decimal('long_out', 11, 8)->nullable();
            $table->unsignedInteger('distance_in_meters')->nullable();
            $table->string('proof_photo')->nullable(); // Selfie photo path
            
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Micro-adjustment #1: Compound Unique Index
            $table->unique(['employee_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
