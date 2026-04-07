<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // -----------------------------------------------
    // POST /api/auth/register
    // -----------------------------------------------
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'token'   => $token,
            'user'    => new UserResource($user),
        ], 201);
    }

    // -----------------------------------------------
    // POST /api/auth/login
    // -----------------------------------------------
    public function login(LoginRequest $request): JsonResponse
    {
        // check credentials
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid email or password',
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        // check if account is active
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Your account has been suspended',
            ], 403);
        }

        // revoke old tokens (single device login)
        // remove this if you want multi-device login
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => new UserResource($user),
        ]);
    }

    // -----------------------------------------------
    // POST /api/auth/logout
    // -----------------------------------------------
    public function logout(): JsonResponse
    {
        // revoke only current device token
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    // -----------------------------------------------
    // GET /api/auth/me
    // -----------------------------------------------
    public function me(): JsonResponse
    {
        return response()->json([
            'user' => new UserResource(auth()->user()),
        ]);
    }
}