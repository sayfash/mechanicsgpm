<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('customer_id', 50)->nullable();
            $table->string('make', 50);
            $table->string('model', 50);
            $table->string('vehicle_type', 50)->nullable();
            $table->string('color', 30)->nullable();
            $table->integer('year')->default(2024);
            $table->string('license_plate', 20)->unique();
            $table->string('vin', 50)->nullable()->unique();
            $table->string('engine_number', 50)->nullable();
            $table->string('controller_number', 50)->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
