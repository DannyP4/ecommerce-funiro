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
            CategorySeeder::class,
            CustomerSeeder::class,
            DashboardSeeder::class,
            FeedbackSeeder::class,
            OrderSeeder::class,
            ProductSeeder::class,
            UserSeeder::class,
            RoleSeeder::class,
        ]);
    }
}
