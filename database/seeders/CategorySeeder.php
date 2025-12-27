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
            ['name' => 'Living Room', 'image' => 'images/category/livingroom.jpg'],
            ['name' => 'Bedroom', 'image' => 'images/category/bedroom.png'],
            ['name' => 'Dining Room', 'image' => 'images/category/diningroom.jpg'],
            ['name' => 'Office', 'image' => 'images/category/office.jpg'],
            ['name' => 'Kitchen', 'image' => 'images/category/kitchen.jpg'],
            ['name' => 'Bathroom', 'image' => 'images/category/bathroom.jpeg'],
            ['name' => 'Outdoor', 'image' => 'images/category/outdoor.jpg'],
            ['name' => 'Storage', 'image' => 'images/category/storage.jpg'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
