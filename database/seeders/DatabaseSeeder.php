<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            SettingSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class, // ⭐ ADD THIS
        ]);
    }
}