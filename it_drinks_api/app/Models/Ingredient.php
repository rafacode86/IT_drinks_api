<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    /** @use HasFactory<\Database\Factories\IngredientFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'origin',
        'classification',
    ];

    public function cocktails(){
        return $this->belongsToMany(Cocktail::class)
                    ->withTimestamps()
                    ->withPivot('measure');
    }
}
