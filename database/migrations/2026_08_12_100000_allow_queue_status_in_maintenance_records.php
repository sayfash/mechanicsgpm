<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            // Change status column type to varchar(50) so queue, in_progress, completed etc are all supported
            DB::statement("ALTER TABLE maintenance_records MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending'");
        } catch (\Throwable $e) {
            // Fallback for SQLite or other DB engines if needed
        }
    }

    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE maintenance_records MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending'");
        } catch (\Throwable $e) {
        }
    }
};
