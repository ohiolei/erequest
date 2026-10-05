<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            MenuSeeder::class,
            AdminSeeder::class,
            RegistryUserSeeder::class,
            BursaryUserSeeder::class,
            ExamsRecordsUserSeeder::class,
            StudentUserSeeder::class,
        ]);
    }
}
