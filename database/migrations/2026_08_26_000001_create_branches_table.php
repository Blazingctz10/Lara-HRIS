<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. HO-001, KC-001, KCP-010
            $table->string('name');
            $table->enum('type', ['Head Office', 'Main Branch', 'Sub Branch', 'Cash Office'])->default('Main Branch');
            $table->string('city');
            $table->text('address');
            $table->string('phone')->nullable();
            
            // Geofencing
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->unsignedInteger('radius_meters')->default(50);
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
