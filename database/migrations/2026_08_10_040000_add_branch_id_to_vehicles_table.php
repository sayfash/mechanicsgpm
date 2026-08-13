<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('vehicles', 'branch_id')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('customer_id')->constrained('branches')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('vehicles', 'branch_id')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};
