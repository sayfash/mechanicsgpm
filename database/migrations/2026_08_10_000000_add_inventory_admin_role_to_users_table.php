<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'shop_admin', 'inventory_admin', 'mechanic') NOT NULL");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'shop_admin', 'mechanic') NOT NULL");
        }
    }
};
