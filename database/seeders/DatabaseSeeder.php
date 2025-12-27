<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Role;
use Illuminate\Database\Seeder;
use ParagonIE\Sodium\Core\Curve25519\Fe;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
            CustomerSeeder::class,
            ProductSeeder::class,
            UserSeeder::class,
            OrderSeeder::class,
            FeedbackSeeder::class,
            DashboardSeeder::class,
        ]);
    }
}
