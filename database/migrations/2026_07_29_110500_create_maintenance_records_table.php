<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->string('job_id', 30)->unique()->nullable();
            $table->string('vehicle_id', 50);
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->foreignId('mechanic_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('repair_category', 50)->default('Repair');
            $table->text('description');
            $table->integer('km_reached')->nullable();
            $table->text('common_issues')->nullable();
            $table->text('other_issues')->nullable();
            $table->string('service_sku', 50)->nullable();
            $table->string('service_name', 100)->nullable();
            $table->decimal('labor_fee', 12, 2)->default(0.00);
            $table->string('other_expenses_category', 100)->nullable();
            $table->decimal('other_expenses_fee', 12, 2)->default(0.00);
            $table->date('repair_date')->nullable();
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->text('notes')->nullable();
            $table->string('payment_method', 50)->default('Cash');
            $table->decimal('parts_labor_paid', 12, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2)->default(0.00);
            $table->string('photo_path', 255)->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->timestamps();

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
