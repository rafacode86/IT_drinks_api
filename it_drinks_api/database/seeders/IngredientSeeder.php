<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ingredient;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = [
            ['name' => 'Vodka',        'type' => 'Smirnof', 'origin' => 'Russia',      'classification' => 'alcoholic', 'alcohol_content' => 40],
            ['name' => 'Gin',          'type' => 'Bombay Saphire', 'origin' => 'UK',          'classification' => 'alcoholic', 'alcohol_content' => 40],
            ['name' => 'Tequila',      'type' => 'Jose Cuervo', 'origin' => 'Mexico',      'classification' => 'alcoholic', 'alcohol_content' => 38],
            ['name' => 'Ron',          'type' => 'Barceló', 'origin' => 'Caribbean',   'classification' => 'alcoholic', 'alcohol_content' => 40],
            ['name' => 'Whiskey',      'type' => 'Chivas', 'origin' => 'Scotland',    'classification' => 'alcoholic', 'alcohol_content' => 40],
            ['name' => 'Triple Sec',   'type' => 'liqueur','origin' => 'France',      'classification' => 'alcoholic', 'alcohol_content' => 30],
            ['name' => 'Vermouth',     'type' => 'fortified wine','origin' => 'Italy','classification' => 'alcoholic', 'alcohol_content' => 16],
            ['name' => 'Lime Juice',   'type' => 'fruit',  'origin' => '—',           'classification' => 'juice',     'alcohol_content' => 0],
            ['name' => 'Lemon Juice',  'type' => 'fruit',  'origin' => '—',           'classification' => 'juice',     'alcohol_content' => 0],
            ['name' => 'Simple Syrup', 'type' => 'syrup',  'origin' => '—',           'classification' => 'garnish',   'alcohol_content' => 0],
            ['name' => 'Soda Water',   'type' => 'soda',   'origin' => '—',           'classification' => 'soda',      'alcohol_content' => 0],
            ['name' => 'Cola',         'type' => 'CocaCola',   'origin' => '—',           'classification' => 'soda',      'alcohol_content' => 0],
            ['name' => 'Tonic Water',  'type' => 'NOrdic myst',   'origin' => '—',           'classification' => 'soda',      'alcohol_content' => 0],
            ['name' => 'Mint Leaves',  'type' => 'herb',   'origin' => '—',           'classification' => 'garnish',   'alcohol_content' => 0],
        ];
    }
}
