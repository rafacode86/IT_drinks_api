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
            $volume = $ingredient->pivot->measure_ml;
            $alcoholPercentage = $ingredient->alcohol_content;

            $totalVolume += $volume;
            $totalAlcohol += ($volume * $alcoholPercentage);
        }

        return $totalVolume > 0 ? round($totalAlcohol / $totalVolume, 2) : 0;
    }

}
