<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'type' => $this->faker->optional()->randomElement(['run', 'vodka', 'gin', 'cola', 'herb', 'tonic', 'orange juice']),
            'origin' => $this->faker->optional()->country(),
            'classification' => $this->faker->randomElement(['alcoholic', 'soda', 'juice', 'garnish']),
        ];
    }
}
