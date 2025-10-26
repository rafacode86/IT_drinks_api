<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

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