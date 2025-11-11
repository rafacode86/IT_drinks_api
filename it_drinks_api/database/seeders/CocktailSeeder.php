<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cocktail;
use App\Models\Ingredient;

class CocktailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recipes = [
            'Margarita' => [
                ['name' => 'Tequila',     'ml' => 50],
                ['name' => 'Triple Sec',  'ml' => 20],
                ['name' => 'Lime Juice',  'ml' => 30],
            ],
            'Mojito' => [
                ['name' => 'Rum',         'ml' => 50],
                ['name' => 'Lime Juice',  'ml' => 25],
                ['name' => 'Simple Syrup','ml' => 15],
                ['name' => 'Soda Water',  'ml' => 60],
                ['name' => 'Mint Leaves', 'ml' => 5],
            ],
            'Old Fashioned' => [
                ['name' => 'Whiskey',     'ml' => 60],
                ['name' => 'Simple Syrup','ml' => 10],
            ],
            'Vodka Tonic' => [
                ['name' => 'Vodka',       'ml' => 50],
                ['name' => 'Tonic Water', 'ml' => 120],
            ],
            'Gin Tonic' => [
                ['name' => 'Gin',         'ml' => 50],
                ['name' => 'Tonic Water', 'ml' => 120],
            ],
            'Negroni' => [
                ['name' => 'Gin',         'ml' => 30],
                ['name' => 'Vermouth',    'ml' => 30],
                ['name' => 'Triple Sec',  'ml' => 10], // ejemplo adaptado
            ],
        ];

        foreach ($recipes as $cocktailName => $items) {
            $cocktail = Cocktail::updateOrCreate(
                ['name' => $cocktailName],
                ['description' => null, 'type' => null]
            );

            $pivotData = [];

            foreach ($items as $row) {
                $ingredient = Ingredient::where('name', $row['name'])->first();
                if (!$ingredient) {
                    continue; // o lanza excepción si prefieres estricto
                }
                $pivotData[$ingredient->id] = ['measure_ml' => (float) $row['ml']];
            }

            $cocktail->ingredients()->sync($pivotData);
        }
    }
}
