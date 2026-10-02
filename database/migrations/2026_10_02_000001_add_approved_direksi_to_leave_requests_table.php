<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Untuk SQLite / MySQL, pastikan kolom status mendukung Approved_Direksi
        if (config('database.default') !== 'sqlite') {
            Schema::table('leave_requests', function (Blueprint $table) {
                $table->string('status', 40)->default('Pending')->change();
            });
        }
    }

    public function down(): void
    {
        //
    }
};
