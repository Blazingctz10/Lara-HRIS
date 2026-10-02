<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF;');
            DB::statement('CREATE TABLE IF NOT EXISTS "leave_requests_temp" (
                "id" integer primary key autoincrement not null,
                "employee_id" integer not null,
                "leave_type_id" integer not null,
                "start_date" date not null,
                "end_date" date not null,
                "total_days" integer not null,
                "reason" text not null,
                "attachment_url" varchar,
                "status" varchar not null default "Pending",
                "approved_by" integer,
                "rejection_reason" text,
                "created_at" datetime,
                "updated_at" datetime,
                foreign key("employee_id") references "employees"("id") on delete cascade,
                foreign key("leave_type_id") references "leave_types"("id") on delete cascade,
                foreign key("approved_by") references "users"("id") on delete set null
            );');
            DB::statement('INSERT INTO "leave_requests_temp" ("id", "employee_id", "leave_type_id", "start_date", "end_date", "total_days", "reason", "attachment_url", "status", "approved_by", "rejection_reason", "created_at", "updated_at") SELECT "id", "employee_id", "leave_type_id", "start_date", "end_date", "total_days", "reason", "attachment_url", "status", "approved_by", "rejection_reason", "created_at", "updated_at" FROM "leave_requests";');
            DB::statement('DROP TABLE "leave_requests";');
            DB::statement('ALTER TABLE "leave_requests_temp" RENAME TO "leave_requests";');
            DB::statement('PRAGMA foreign_keys=ON;');
        } else {
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
