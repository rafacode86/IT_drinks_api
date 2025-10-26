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

class AuthController extends Controller
{
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

        $token = $user->createToken('API Token')->accessToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);

    }

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

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->token();
        $token->revoke();

        return response()->json(['message' => 'Logged out successfully.'], 200);
    }
}
