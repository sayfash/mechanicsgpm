<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('module_location', 100)->nullable()->after('target_table');
            $table->string('action_summary', 255)->nullable()->after('module_location');
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn(['module_location', 'action_summary']);
        });
    }
};
