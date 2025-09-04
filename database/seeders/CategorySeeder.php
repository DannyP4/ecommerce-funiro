<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Living Room', 'image' => 'storage/images/categories/living-room.jpg'],
            ['name' => 'Bedroom', 'image' => 'storage/images/categories/bedroom.jpg'],
            ['name' => 'Dining Room', 'image' => 'storage/images/categories/dining-room.jpg'],
            ['name' => 'Office', 'image' => 'storage/images/categories/office.jpg'],
            ['name' => 'Kitchen', 'image' => 'storage/images/categories/kitchen.jpg'],
            ['name' => 'Bathroom', 'image' => 'storage/images/categories/bathroom.jpg'],
            ['name' => 'Outdoor', 'image' => 'storage/images/categories/outdoor.jpg'],
            ['name' => 'Storage', 'image' => 'storage/images/categories/storage.jpg'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
