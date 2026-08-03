<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Branches
        $branches = [
            ['name' => 'Downtown Main Office', 'location' => '101 Main St, City Center'],
            ['name' => 'Northside Auto Care', 'location' => '405 Oak Ave, Northside'],
            ['name' => 'East End Repair Hub', 'location' => '789 Pine Rd, East End'],
            ['name' => 'Westside Service Hub', 'location' => '321 Elm Blvd, Westside'],
            ['name' => 'South Valley Shop', 'location' => '555 Cedar St, South Valley'],
        ];

        foreach ($branches as $branch) {
            Branch::firstOrCreate(['name' => $branch['name']], $branch);
        }

        // 2. Seed Users
        $users = [
            [
                'username' => 'superadmin',
                'password_hash' => Hash::make('password123'),
                'role' => 'super_admin',
                'branch_id' => null,
            ],
            [
                'username' => 'shopadmin1',
                'password_hash' => Hash::make('password123'),
                'role' => 'shop_admin',
                'branch_id' => 1,
            ],
            [
                'username' => 'mechanic1',
                'password_hash' => Hash::make('password123'),
                'role' => 'mechanic',
                'branch_id' => 1,
            ]
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['username' => $user['username']], $user);
        }

        // 3. Seed Main Sparepart Categories
        $mainCategories = ['General', 'Bearing', 'Brake Pad', 'Shock Breaker', 'Tire'];
        foreach ($mainCategories as $catName) {
            if (\Illuminate\Support\Facades\Schema::hasTable('sparepart_categories')) {
                \Illuminate\Support\Facades\DB::table('sparepart_categories')->updateOrInsert(
                    ['name' => $catName],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}
