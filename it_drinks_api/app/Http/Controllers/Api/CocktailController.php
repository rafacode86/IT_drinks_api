<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cocktail;

class CocktailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cokctails = Cocktail::with('ingredients')->get();
        return response()->json($cokctails, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cocktail = Cocktail::with('ingredients')->find($id);

        if (!$cocktail) {
            return response()->json(['message' => 'Cocktail not found'], 404);
        }

        return response()->json($cocktail, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
