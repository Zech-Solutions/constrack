<?php

namespace Database\Factories;

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
            'name' => fake()->name(),
            'price' => fake()->randomFloat(2, 10, 500),
            'description' => fake()->text(),
            'stock' => fake()->numberBetween(1, 100),
            'sku' => fake()->unique()->isbn10(),
            'category_id' => fake()->numberBetween(1, 10),
            'supplier_id' => fake()->numberBetween(1, 10),
            'image' => 'https://picsum.photos/300/200?random=' . $this->faker->unique()->numberBetween(1, 1000),
            'is_active' => fake()->boolean(),
        ];
    }
}
