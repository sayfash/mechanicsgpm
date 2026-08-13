<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('record_parts_used') && !Schema::hasColumn('record_parts_used', 'is_claimed')) {
            Schema::table('record_parts_used', function (Blueprint $table) {
                $table->boolean('is_claimed')->default(false)->after('is_charged');
                $table->string('warranty_category', 50)->default('Unclaimable / No Warranty')->after('is_claimed');
            });
        }

        if (Schema::hasTable('inventory') && !Schema::hasColumn('inventory', 'warranty_category')) {
            Schema::table('inventory', function (Blueprint $table) {
                $table->string('warranty_category', 50)->default('Unclaimable / No Warranty')->after('category');
            });
        }

        if (Schema::hasTable('vehicles') && !Schema::hasColumn('vehicles', 'activate_date')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->date('activate_date')->nullable()->after('year');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('record_parts_used')) {
            Schema::table('record_parts_used', function (Blueprint $table) {
                if (Schema::hasColumn('record_parts_used', 'is_claimed')) $table->dropColumn('is_claimed');
                if (Schema::hasColumn('record_parts_used', 'warranty_category')) $table->dropColumn('warranty_category');
            });
        }

        if (Schema::hasTable('inventory') && Schema::hasColumn('inventory', 'warranty_category')) {
            Schema::table('inventory', function (Blueprint $table) {
                $table->dropColumn('warranty_category');
            });
        }

        if (Schema::hasTable('vehicles') && Schema::hasColumn('vehicles', 'activate_date')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropColumn('activate_date');
            });
        }
    }
};
