<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'division_scope')) {
                $table->string('division_scope', 40)->nullable()->after('role');
            }
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('leave_requests', 'leave_type_custom')) {
                $table->string('leave_type_custom', 150)->nullable()->after('leave_type_id');
            }
            if (! Schema::hasColumn('leave_requests', 'division_category')) {
                $table->string('division_category', 40)->nullable()->after('leave_type_custom');
            }
            if (! Schema::hasColumn('leave_requests', 'approved_leader_by')) {
                $table->unsignedBigInteger('approved_leader_by')->nullable()->after('approved_by');
            }
            if (! Schema::hasColumn('leave_requests', 'leader_signed_at')) {
                $table->timestamp('leader_signed_at')->nullable()->after('approved_leader_by');
            }
            if (! Schema::hasColumn('leave_requests', 'leader_signature_name')) {
                $table->string('leader_signature_name', 150)->nullable()->after('leader_signed_at');
            }
            if (! Schema::hasColumn('leave_requests', 'direksi_signed_at')) {
                $table->timestamp('direksi_signed_at')->nullable()->after('leader_signature_name');
            }
            if (! Schema::hasColumn('leave_requests', 'direksi_signature_name')) {
                $table->string('direksi_signature_name', 150)->nullable()->after('direksi_signed_at');
            }
            if (! Schema::hasColumn('leave_requests', 'acc_at')) {
                $table->timestamp('acc_at')->nullable()->after('direksi_signature_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $cols = [
                'leave_type_custom',
                'division_category',
                'approved_leader_by',
                'leader_signed_at',
                'leader_signature_name',
                'direksi_signed_at',
                'direksi_signature_name',
                'acc_at',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('leave_requests', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'division_scope')) {
                $table->dropColumn('division_scope');
            }
        });
    }
};
