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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ingredients' => 'array',
            'ingredients.*.id' => 'exists:ingredients,id',
            'ingredients.*.measure_ml' => 'numeric|min:0',
        ]);

        $cocktail = Cocktail::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (!empty($validated['ingredients'])) {
            $pivotData = [];
            foreach ($validated['ingredients'] as $ingredient) {
                $pivotData[$ingredient['id']] = ['measure_ml' => $ingredient['measure_ml']];
            }
            $cocktail->ingredients()->sync($pivotData);
        }

        return response()->json($cocktail->load('ingredients'), 201);
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
        $cocktail = Cocktail::find($id);

        if (!$cocktail) {
            return response()->json(['message' => 'Cocktail not found'], 404);
        }

        $cocktail->update($request->only(['name', 'description']));

        if ($request->has('ingredients')) {
            $pivotData = [];
            foreach ($request->ingredients as $ingredient) {
                $pivotData[$ingredient['id']] = ['measure_ml' => $ingredient['measure_ml']];
            }
            $cocktail->ingredients()->sync($pivotData);
        }

        return response()->json($cocktail->load('ingredients'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
