<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('record_parts_used', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_record_id')->constrained('maintenance_records')->onDelete('cascade');
            $table->foreignId('inventory_id')->constrained('inventory')->onDelete('restrict');
            $table->integer('quantity_used')->default(1);
            $table->decimal('price_at_use', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('record_parts_used');
    }
};
