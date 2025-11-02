<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cocktail extends Model
{
    /** @use HasFactory<\Database\Factories\CocktailFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
    ];

    public function ingredients(){
        return $this->belongsToMany(Ingredient::class, 'cocktail_ingredient')
                    ->withTimestamps()
                    ->withPivot('measure_ml');
    }

    public function calculateAlcoholContent(): float
    {

        $ingredients = $this->ingredients;

        if ($ingredients->isEmpty()) {
            return 0;
        }

        $totalVolume = 0;
        $totalAlcohol = 0;  

        foreach ($ingredients as $ingredient) {
        
            $volume = (float) $ingredient->pivot->measure_ml;
            $totalVolume += $volume;

            if ($ingredient->alcohol_content > 0) {
                $alcoholPercentage = (float) $ingredient->alcohol_content / 100;
                $totalAlcohol += $volume * $alcoholPercentage;
            }
        }

        return $totalVolume > 0
            ? round(($totalAlcohol / $totalVolume) * 100, 2): 0;
        }

}
