<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('record_parts_used') && !Schema::hasColumn('record_parts_used', 'is_charged')) {
            Schema::table('record_parts_used', function (Blueprint $table) {
                $table->boolean('is_charged')->default(true)->after('price_at_use');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('record_parts_used') && Schema::hasColumn('record_parts_used', 'is_charged')) {
            Schema::table('record_parts_used', function (Blueprint $table) {
                $table->dropColumn('is_charged');
            });
        }
    }
};
