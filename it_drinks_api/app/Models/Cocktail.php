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
}
