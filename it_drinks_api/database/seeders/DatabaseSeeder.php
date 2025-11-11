<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ingredient;
use App\Models\Cocktail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::factory(3)->create();

        $ingredients = Ingredient::factory(15)->create();

        //dump($ingredients->count());

        Cocktail::factory(5)->create()->each(function ($cocktail) use ($ingredients) {
            $selected = $ingredients->random(rand(2, 5))->pluck('id')->toArray();
            //dump($ingredients->pluck('id')->toArray());
                $relations = [];
                foreach ($selected as $id) {
                    $relations[$id] = ['measure_ml' => fake()->randomFloat(1, 10, 100)];
                }
                
                //dump($relations);

                $cocktail->ingredients()->sync($relations);
            
            
        });

         $this->call([
            IngredientSeeder::class,
            CocktailSeeder::class,
        ]);


    }
}
