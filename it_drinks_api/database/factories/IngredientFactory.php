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
        $classification = $this->faker->randomElement(['alcoholic', 'soda', 'juice', 'garnish']);

        return [
            'name' => $this->faker->unique()->word(),
            'type' => $this->faker->optional()->randomElement(['run', 'vodka', 'gin', 'cola', 'herb', 'tonic', 'orange juice']),
            'origin' => $this->faker->optional()->country(),
            'classification' => $classification,
            'alcohol_content' => $classification === 'alcoholic'
                ? $this->faker->randomFloat(1, 5, 60)
                : 0,
        ];
    }
}
