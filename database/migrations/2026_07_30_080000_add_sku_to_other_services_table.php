<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('other_services') && !Schema::hasColumn('other_services', 'sku')) {
            Schema::table('other_services', function (Blueprint $table) {
                $table->string('sku', 50)->nullable()->after('name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('other_services') && Schema::hasColumn('other_services', 'sku')) {
            Schema::table('other_services', function (Blueprint $table) {
                $table->dropColumn('sku');
            });
        }
    }
};
