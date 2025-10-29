<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cocktail;
use App\Models\Ingredient;

/**
 * @OA\Tag(
 *     name="Cocktails",
 *     description="Gestión de cócteles y operaciones especiales. Solo usuarios autenticados pueden acceder. Los administradores pueden crear, actualizar y eliminar, los usuarios solo visualizar."
 * )
 */
class CocktailController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * @OA\Get(
     *     path="/api/cocktails",
     *     tags={"Cocktails"},
     *     summary="Listar todos los cócteles",
     *     description="Devuelve una lista de todos los cócteles con sus ingredientes. Accesible para cualquier usuario autenticado.",
     *     security={{"passport":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de cócteles",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Mojito"),
     *                 @OA\Property(property="description", type="string", example="Cóctel clásico cubano"),
     *                 @OA\Property(
     *                     property="ingredients",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="name", type="string", example="Ron Blanco"),
     *                         @OA\Property(property="pivot", type="object",
     *                             @OA\Property(property="measure_ml", type="number", example=50)
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function index()
    {
        $cokctails = Cocktail::with('ingredients')->get();
        return response()->json($cokctails, 200);
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * @OA\Post(
     *     path="/api/cocktails",
     *     tags={"Cocktails"},
     *     summary="Crear un nuevo cóctel",
     *     description="Permite a los administradores crear un cóctel y asociar sus ingredientes con cantidades en mililitros.",
     *     security={{"passport":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Negroni"),
     *             @OA\Property(property="description", type="string", example="Cóctel italiano amargo"),
     *             @OA\Property(
     *                 property="ingredients",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=3),
     *                     @OA\Property(property="measure_ml", type="number", example=30)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Cóctel creado correctamente"),
     *     @OA\Response(response=403, description="Prohibido (solo admin)"),
     *     @OA\Response(response=422, description="Datos inválidos")
     * )
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

    /**
     * @OA\Get(
     *     path="/api/cocktails/{id}",
     *     tags={"Cocktails"},
     *     summary="Ver un cóctel específico",
     *     description="Muestra la información detallada de un cóctel con sus ingredientes.",
     *     security={{"passport":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del cóctel",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Cóctel encontrado correctamente"),
     *     @OA\Response(response=404, description="Cóctel no encontrado"),
     *     @OA\Response(response=401, description="No autenticado")
     * )
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

    /**
     * @OA\Put(
     *     path="/api/cocktails/{id}",
     *     tags={"Cocktails"},
     *     summary="Actualizar un cóctel existente",
     *     description="Permite a los administradores actualizar los datos de un cóctel y sus ingredientes asociados.",
     *     security={{"passport":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID del cóctel", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Negroni Actualizado"),
     *             @OA\Property(property="description", type="string", example="Versión mejorada del clásico Negroni"),
     *             @OA\Property(
     *                 property="ingredients",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="measure_ml", type="number", example=25)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Cóctel actualizado correctamente"),
     *     @OA\Response(response=404, description="Cóctel no encontrado"),
     *     @OA\Response(response=403, description="Prohibido (solo admin)")
     * )
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

    /**
     * @OA\Delete(
     *     path="/api/cocktails/{id}",
     *     tags={"Cocktails"},
     *     summary="Eliminar un cóctel",
     *     description="Permite a los administradores eliminar un cóctel del sistema.",
     *     security={{"passport":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID del cóctel", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Cóctel eliminado correctamente"),
     *     @OA\Response(response=404, description="Cóctel no encontrado"),
     *     @OA\Response(response=403, description="Prohibido (solo admin)")
     * )
     */
    public function destroy(string $id)
    {
        $cocktail = Cocktail::find($id);

        if (!$cocktail) {
            return response()->json(['message' => 'Cocktail not found'], 404);
        }

        $cocktail->delete();
        return response()->json(['message' => 'Cocktail deleted successfully'], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/search/{ingredientId}",
     *     tags={"Cocktails"},
     *     summary="Buscar cócteles por ingrediente",
     *     description="Permite buscar cócteles que contengan un ingrediente específico.",
     *     security={{"passport":{}}},
     *     @OA\Parameter(
     *         name="ingredientId",
     *         in="path",
     *         required=true,
     *         description="ID del ingrediente a buscar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Lista de cócteles que contienen el ingrediente"),
     *     @OA\Response(response=404, description="No se encontraron cócteles con ese ingrediente"),
     *     @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function searchCocktailsByIngredient(string $ingredientId) {

        $cocktails = Cocktail::whereHas('ingredients', function ($query) use ($ingredientId) {
            $query->where('ingredients.id', $ingredientId);
            })->with('ingredients')->get();

        if ( $cocktails->isEmpty() ) {
            return response()->json(['message' => 'No cocktails found with the specified ingredient'], 404);
        }

        return response()->json($cocktails, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/cocktails/{id}/alcohol-content",
     *     tags={"Cocktails"},
     *     summary="Calcular contenido alcohólico de un cóctel",
     *     description="Calcula el contenido de alcohol en base al porcentaje de alcohol de sus ingredientes y sus cantidades (measure_ml).",
     *     security={{"passport":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID del cóctel", @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Porcentaje alcohólico calculado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="cocktail", type="string", example="Margarita"),
     *             @OA\Property(property="alcohol_content", type="string", example="12.5%")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Cóctel no encontrado"),
     *     @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function showAlcoholContent($id)
    {
        $cocktail = Cocktail::with('ingredients')->find($id);

        if (!$cocktail) {
            return response()->json(['message' => 'Cocktail not found'], 404);
        }

        return response()->json([
            'cocktail' => $cocktail->name,
            'alcohol_content' => $cocktail->calculateAlcoholContent() . '%',
        ]);
    }

}
