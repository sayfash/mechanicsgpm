<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('maintenance_records', 'daily_queue_number')) {
            Schema::table('maintenance_records', function (Blueprint $table) {
                $table->integer('daily_queue_number')->nullable()->after('branch_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('maintenance_records', 'daily_queue_number')) {
            Schema::table('maintenance_records', function (Blueprint $table) {
                $table->dropColumn('daily_queue_number');
            });
        }
    }
};
