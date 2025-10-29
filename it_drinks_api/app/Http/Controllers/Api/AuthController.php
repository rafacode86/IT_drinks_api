<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\PersonalAccessTokenFactory;
use Illuminate\Http\JsonResponse;


/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Endpoints para registro, autenticación y cierre de sesión de usuarios."
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="passport",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Usa el token Bearer devuelto tras iniciar sesión o registrarte."
 * )
 */

class AuthController extends Controller
{   
     /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Auth"},
     *     summary="Registrar un nuevo usuario",
     *     description="Permite crear una nueva cuenta de usuario. Por defecto, el rol asignado es 'user'.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Rafa"),
     *             @OA\Property(property="email", type="string", example="rafa@example.com"),
     *             @OA\Property(property="password", type="string", example="12345678"),
     *             @OA\Property(property="password_confirmation", type="string", example="12345678")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuario registrado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuario registrado correctamente"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Rafa"),
     *                 @OA\Property(property="email", type="string", example="rafa@example.com"),
     *                 @OA\Property(property="role", type="string", example="user")
     *             ),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Datos inválidos")
     * )
     */
    public function register(Request $request) {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        $tokenFactory = app(PersonalAccessTokenFactory::class);
        $tokenResult = $tokenFactory->make($user->id, 'API Token', ['user']);
        $token = $tokenResult->accessToken;

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'user' => $user,
            'token' => $token,
        ], 201);

    }
    
    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Auth"},
     *     summary="Iniciar sesión",
     *     description="Autentica un usuario existente y devuelve su token de acceso.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", example="rafa@example.com"),
     *             @OA\Property(property="password", type="string", example="12345678")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inicio de sesión correcto",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Inicio de sesión correcto"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Rafa"),
     *                 @OA\Property(property="email", type="string", example="rafa@example.com"),
     *                 @OA\Property(property="role", type="string", example="user")
     *             ),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *             @OA\Property(property="scopes", type="array", @OA\Items(type="string", example="user"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Credenciales incorrectas")
     * )
     */

    public function login(Request $request) {

        $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciales incorrectas.'],
            ]);
        }

        $user = Auth::user();

        $tokenFactory = app(PersonalAccessTokenFactory::class);

        $scopes = $user->role === 'admin'
            ? ['admin', 'user']
            : ['user'];

        $tokenResult = $tokenFactory->make($user->id, 'API Token', $scopes);
        $token = $tokenResult->accessToken;

        return response()->json([
            'message' => 'Inicio de sesión correcto',
            'user' => $user,
            'token' => $token,
            'scopes' => $scopes,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Auth"},
     *     summary="Cerrar sesión",
     *     description="Revoca el token de acceso actual. Requiere autenticación mediante Passport.",
     *     security={{"passport":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sesión cerrada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out successfully.")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token inválido o no proporcionado")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->token();
        $token->revoke();

        return response()->json(['message' => 'Logged out successfully.'], 200);
    }
}
