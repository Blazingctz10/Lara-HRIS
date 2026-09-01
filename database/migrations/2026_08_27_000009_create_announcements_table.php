<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->default('internal_news'); // direksi_memo, ojk_compliance, internal_news, holiday
            $table->string('priority')->default('normal'); // normal, important, urgent
            $table->text('content');
            $table->string('attachment_url')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete(); // null = national/all branches
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->date('published_at')->default(now());
            $table->date('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
