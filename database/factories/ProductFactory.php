<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Syltherine Chair',
                'Leviosa Sofa',
                'Lolito Bed',
                'Respira Table',
                'Grifo Lamp',
                'Muggo Cabinet',
                'Pingky Wardrobe',
                'Potty Plant Stand'
            ]),
            'description' => fake()->sentence(6),
            'price' => fake()->numberBetween(500000, 15000000),
            'category_id' => Category::factory(),
            'image' => null,
        ];
    }
}