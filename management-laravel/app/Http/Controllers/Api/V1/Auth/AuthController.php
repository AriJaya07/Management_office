<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\Auth\RegisterUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUser $registerUser): JsonResponse
    {
        $user = $registerUser->handle($request->validated());

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Registered successfully',
            'user' => new UserResource($user),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->validated())) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        if (! $user->is_active) {
            Auth::guard('web')->logout();

            throw ValidationException::withMessages([
                'email' => __('Your account has been deactivated.'),
            ]);
        }

        $request->session()->regenerate();

        return response()->json([
            'message' => 'Logged in successfully',
            'user' => new UserResource($user->load(['department', 'position'])),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user()->load(['department', 'position']));
    }
}
