<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ingredient;


/**
 * @OA\Tag(
 *     name="Ingredients",
 *     description="Gestión de ingredientes (CRUD) (solo administradores pueden crear, editar o eliminar)"
 * )
 */

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * @OA\Get(
     *     path="/api/ingredients",
     *     tags={"Ingredients"},
     *     summary="Listar todos los ingredientes",
     *     description="Devuelve una lista completa de ingredientes. Requiere autenticación (admin o user).",
     *     security={{"passport":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de ingredientes obtenida correctamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Vodka"),
     *                 @OA\Property(property="type", type="string", example="Spirit"),
     *                 @OA\Property(property="classification", type="string", example="alcoholic"),
     *                 @OA\Property(property="alcohol_content", type="number", example=40),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token no válido o ausente")
     * )
     */
    public function index()
    {
        $ingredients = Ingredient::all();
        return response()->json($ingredients, 200);
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * @OA\Post(
     *     path="/api/ingredients",
     *     tags={"Ingredients"},
     *     summary="Crear un nuevo ingrediente",
     *     description="Permite a los administradores crear un nuevo ingrediente en la base de datos.",
     *     security={{"passport":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","classification"},
     *             @OA\Property(property="name", type="string", example="Gin"),
     *             @OA\Property(property="type", type="string", example="Bebida"),
     *             @OA\Property(property="origin", type="string", example="Inglaterra"),
     *             @OA\Property(property="classification", type="string", enum={"alcoholic","soda","juice","garnish"}, example="alcoholic"),
     *             @OA\Property(property="alcohol_content", type="number", format="float", example=37.5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ingrediente creado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=5),
     *             @OA\Property(property="name", type="string", example="Gin"),
     *             @OA\Property(property="classification", type="string", example="alcoholic"),
     *             @OA\Property(property="alcohol_content", type="number", example=37.5)
     *         )
     *     ),
     *     @OA\Response(response=403, description="Prohibido (solo admin)"),
     *     @OA\Response(response=401, description="No autenticado"),
     *     @OA\Response(response=422, description="Datos inválidos")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'origin' => 'nullable|string|max:255',
            'classification' => 'required|in:alcoholic,soda,juice,garnish',
            'alcohol_content' => 'nullable|numeric|min:0|max:100',
        ]);

        $validated['alcohol_content'] = $validated['classification'] === 'alcoholic'
            ? ($validated['alcohol_content'] ?? 0)
            : 0;

        $ingredient = Ingredient::create($validated);
        return response()->json($ingredient, 201);
    }

    /**
     * Display the specified resource.
     */

    /**
     * @OA\Get(
     *     path="/api/ingredients/{id}",
     *     tags={"Ingredients"},
     *     summary="Ver un ingrediente específico",
     *     description="Devuelve la información de un ingrediente concreto. Solo accesible para usuarios autenticados.",
     *     security={{"passport":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del ingrediente",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ingrediente encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Vodka"),
     *             @OA\Property(property="classification", type="string", example="alcoholic"),
     *             @OA\Property(property="alcohol_content", type="number", example=40.0)
     *         )
     *     ),
     *     @OA\Response(response=404, description="Ingrediente no encontrado"),
     *     @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function show(string $id)
    {
        $ingredient = Ingredient::find($id);

        if (!$ingredient) {
            return response()->json(['message' => 'Ingredient not found'], 404);
        }

        return response()->json($ingredient, 200);
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * @OA\Put(
     *     path="/api/ingredients/{id}",
     *     tags={"Ingredients"},
     *     summary="Actualizar un ingrediente existente",
     *     description="Permite a los administradores actualizar la información de un ingrediente.",
     *     security={{"passport":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del ingrediente a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Ron Blanco"),
     *             @OA\Property(property="alcohol_content", type="number", example=37.5)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Ingrediente actualizado correctamente"),
     *     @OA\Response(response=404, description="Ingrediente no encontrado"),
     *     @OA\Response(response=403, description="Prohibido (solo admin)"),
     *     @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function update(Request $request, string $id)
    {
        $ingredient = Ingredient::find($id);

        if (!$ingredient) {
            return response()->json(['message' => 'Ingredient not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|nullable|string|max:255',
            'origin' => 'sometimes|nullable|string|max:255',
            'classification' => 'sometimes|required|in:alcoholic,soda,juice,garnish',
            'alcohol_content' => 'sometimes|nullable|numeric|min:0|max:100',
        ]);

        $classification = $validated['classification'] ?? $ingredient->classification;

        if ($classification !== 'alcoholic') {
            $validated['alcohol_content'] = 0;
        } elseif (array_key_exists('alcohol_content', $validated)) {
            $validated['alcohol_content'] = $validated['alcohol_content'] ?? 0;
        }

        $ingredient->update($validated);
        return response()->json($ingredient, 200);
    }

    /**
     * Remove the specified resource from storage.
     */

    /**
     * @OA\Delete(
     *     path="/api/ingredients/{id}",
     *     tags={"Ingredients"},
     *     summary="Eliminar un ingrediente",
     *     description="Permite a los administradores eliminar un ingrediente del sistema.",
     *     security={{"passport":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del ingrediente a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Ingrediente eliminado correctamente"),
     *     @OA\Response(response=404, description="Ingrediente no encontrado"),
     *     @OA\Response(response=403, description="Prohibido (solo admin)"),
     *     @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function destroy(string $id)
    {
        $ingredient = Ingredient::find($id);

        if (!$ingredient) {
            return response()->json(['message' => 'Ingredient not found'], 404);
        }

        $ingredient->delete();
        return response()->json(['message' => 'Ingredient deleted successfully'], 200);
    }
}
