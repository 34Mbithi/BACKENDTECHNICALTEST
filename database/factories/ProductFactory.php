<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement(['beauty', 'electronics', 'clothing', 'home', 'sports']),
            'price' => fake()->randomFloat(2, 10, 500),
            'discount_percentage' => fake()->optional(0.7)->randomFloat(2, 5, 50),
            'rating' => fake()->optional(0.8)->randomFloat(1, 1, 5),
            'stock' => fake()->numberBetween(0, 100),
            'created_by' => User::factory(),
        ];
    }
}
