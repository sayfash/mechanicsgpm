<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Maintenance Records Indexing
        if (Schema::hasTable('maintenance_records')) {
            Schema::table('maintenance_records', function (Blueprint $table) {
                if (Schema::hasColumn('maintenance_records', 'status')) {
                    $table->index('status', 'idx_maint_records_status');
                }
                if (Schema::hasColumn('maintenance_records', 'created_at')) {
                    $table->index('created_at', 'idx_maint_records_created_at');
                }
                if (Schema::hasColumn('maintenance_records', 'job_id')) {
                    $table->index('job_id', 'idx_maint_records_job_id');
                }
                if (Schema::hasColumn('maintenance_records', 'branch_id') && Schema::hasColumn('maintenance_records', 'status')) {
                    $table->index(['branch_id', 'status'], 'idx_maint_records_branch_status');
                }
            });
        }

        // 2. Inventory Indexing
        if (Schema::hasTable('inventory')) {
            Schema::table('inventory', function (Blueprint $table) {
                if (Schema::hasColumn('inventory', 'sku')) {
                    $table->index('sku', 'idx_inventory_sku');
                }
                if (Schema::hasColumn('inventory', 'part_name')) {
                    $table->index('part_name', 'idx_inventory_part_name');
                }
                if (Schema::hasColumn('inventory', 'category')) {
                    $table->index('category', 'idx_inventory_category');
                }
                if (Schema::hasColumn('inventory', 'branch_id') && Schema::hasColumn('inventory', 'category')) {
                    $table->index(['branch_id', 'category'], 'idx_inventory_branch_category');
                }
            });
        }

        // 3. Vehicles Indexing
        if (Schema::hasTable('vehicles')) {
            Schema::table('vehicles', function (Blueprint $table) {
                if (Schema::hasColumn('vehicles', 'license_plate')) {
                    $table->index('license_plate', 'idx_vehicles_license_plate');
                }
                if (Schema::hasColumn('vehicles', 'vin')) {
                    $table->index('vin', 'idx_vehicles_vin');
                }
                if (Schema::hasColumn('vehicles', 'customer_id')) {
                    $table->index('customer_id', 'idx_vehicles_customer_id');
                }
            });
        }

        // 4. Customers Indexing
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                if (Schema::hasColumn('customers', 'phone')) {
                    $table->index('phone', 'idx_customers_phone');
                }
                if (Schema::hasColumn('customers', 'id_card_number')) {
                    $table->index('id_card_number', 'idx_customers_id_card');
                }
                if (Schema::hasColumn('customers', 'name')) {
                    $table->index('name', 'idx_customers_name');
                }
            });
        }

        // 5. Other Services Indexing
        if (Schema::hasTable('other_services')) {
            Schema::table('other_services', function (Blueprint $table) {
                if (Schema::hasColumn('other_services', 'sku')) {
                    $table->index('sku', 'idx_other_services_sku');
                }
                if (Schema::hasColumn('other_services', 'name')) {
                    $table->index('name', 'idx_other_services_name');
                }
            });
        }

        // 6. Record Parts Used Indexing
        if (Schema::hasTable('record_parts_used')) {
            Schema::table('record_parts_used', function (Blueprint $table) {
                if (Schema::hasColumn('record_parts_used', 'maintenance_record_id')) {
                    $table->index('maintenance_record_id', 'idx_rec_parts_maint_id');
                }
                if (Schema::hasColumn('record_parts_used', 'inventory_id')) {
                    $table->index('inventory_id', 'idx_rec_parts_inv_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('maintenance_records')) {
            Schema::table('maintenance_records', function (Blueprint $table) {
                $table->dropIndex('idx_maint_records_status');
                $table->dropIndex('idx_maint_records_created_at');
                $table->dropIndex('idx_maint_records_job_id');
                $table->dropIndex('idx_maint_records_branch_status');
            });
        }

        if (Schema::hasTable('inventory')) {
            Schema::table('inventory', function (Blueprint $table) {
                $table->dropIndex('idx_inventory_sku');
                $table->dropIndex('idx_inventory_part_name');
                $table->dropIndex('idx_inventory_category');
                $table->dropIndex('idx_inventory_branch_category');
            });
        }

        if (Schema::hasTable('vehicles')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropIndex('idx_vehicles_license_plate');
                $table->dropIndex('idx_vehicles_vin');
                $table->dropIndex('idx_vehicles_customer_id');
            });
        }

        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropIndex('idx_customers_phone');
                $table->dropIndex('idx_customers_id_card');
                $table->dropIndex('idx_customers_name');
            });
        }

        if (Schema::hasTable('other_services')) {
            Schema::table('other_services', function (Blueprint $table) {
                $table->dropIndex('idx_other_services_sku');
                $table->dropIndex('idx_other_services_name');
            });
        }

        if (Schema::hasTable('record_parts_used')) {
            Schema::table('record_parts_used', function (Blueprint $table) {
                $table->dropIndex('idx_rec_parts_maint_id');
                $table->dropIndex('idx_rec_parts_inv_id');
            });
        }
    }
};
