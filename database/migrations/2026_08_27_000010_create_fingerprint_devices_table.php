<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fingerprint_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "Mesin Fingerprint KC Botania"
            $table->string('model')->default('Solution X105');
            $table->string('serial_number')->nullable();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('ip_address')->default('192.168.1.201');
            $table->integer('port')->default(4370);
            $table->string('comm_key')->default('0');
            $table->string('status')->default('Online'); // Online, Standby, Offline
            $table->dateTime('last_sync_at')->nullable();
            $table->integer('total_logs_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fingerprint_devices');
    }
};
