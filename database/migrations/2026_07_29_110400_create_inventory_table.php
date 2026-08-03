<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('sku', 50);
            $table->string('part_name', 100);
            $table->string('unit', 20)->default('Pcs');
            $table->string('category', 100)->default('General');
            $table->string('connected_service', 100)->nullable();
            $table->text('description')->nullable();
            $table->integer('available_qty')->default(0);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['branch_id', 'sku'], 'uq_branch_sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
