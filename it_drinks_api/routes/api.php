<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\CocktailController;

// Ruta pública para registrar usuarios
Route::post('/register', [AuthController::class, 'register']);

// Ruta pública para login
Route::post('/login', [AuthController::class, 'login']);

// Grupo protegido por autenticación
Route::middleware('auth:api')->group(function () {

    // Ruta protegida solo para usuarios autenticados
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'message' => 'Usuario autenticado',
            'user' => $request->user(),
        ]);
    });

    // Ruta protegida con rol específico (usa tu middleware CheckRole)
    Route::middleware('role:admin')->get('/admin/dashboard', function () {
        return response()->json(['message' => 'Bienvenido, Admin']);
    });
});

Route::middleware(['auth:api', 'role:admin'])->group(function () {
    // CRUD completo para admin
    Route::apiResource('ingredients', IngredientController::class);
    Route::apiResource('cocktails', CocktailController::class);
});

Route::middleware(['auth:api', 'role:user'])->group(function () {
    // Solo lectura para usuarios normales
    Route::get('ingredients', [IngredientController::class, 'index']);
    Route::get('cocktails', [CocktailController::class, 'index']);
    Route::get('cocktails/{id}', [CocktailController::class, 'show']);
});