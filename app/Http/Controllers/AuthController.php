<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::with(['role', 'location'])
            ->whereRaw('LOWER(email) = LOWER(?)', [$credentials['email']])
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid email or password',
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'User is inactive',
            ], 403);
        }

        $passwordHash = $user->getAuthPassword();

        if (!Hash::check($credentials['password'], $passwordHash)) {
            return response()->json([
                'message' => 'Invalid email or password',
            ], 401);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'role' => $user->role?->name,
                'location_id' => $user->location_id,
                'location' => $user->location?->name,
                'position' => $user->position,
                'is_active' => $user->is_active,
            ],
        ]);
    }
}