<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('service_options')) {
            Schema::create('service_options', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->unique();
                $table->string('sku', 50)->nullable();
                $table->decimal('fee', 12, 2)->default(0.00);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('other_services')) {
            Schema::create('other_services', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->unique();
                $table->string('sku', 50)->nullable();
                $table->decimal('fee', 12, 2)->default(0.00);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sparepart_categories')) {
            Schema::create('sparepart_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->unique();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('common_issues')) {
            Schema::create('common_issues', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->unique();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('mechanic_form_items')) {
            Schema::create('mechanic_form_items', function (Blueprint $table) {
                $table->id();
                $table->string('label', 100)->unique();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mechanic_form_items');
        Schema::dropIfExists('common_issues');
        Schema::dropIfExists('sparepart_categories');
        Schema::dropIfExists('other_services');
        Schema::dropIfExists('service_options');
    }
};
