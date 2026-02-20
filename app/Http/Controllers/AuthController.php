<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'role' => ['nullable', 'in:creator,brand'],
        ]);

        $user = User::where('email', $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
        }
        if ($request->filled('role') && $user->role !== $request->role) {
            throw ValidationException::withMessages(['email' => ['This account is registered as a ' . $user->role . '. Please sign in with the correct account type.']]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $redirect = match ($user->role) {
            'admin' => url('/admin'),
            'creator' => url('/creator/dashboard'),
            'brand' => url('/brand/dashboard'),
            default => url('/'),
        };
        return response()->json(['user' => $user->only('id', 'name', 'email', 'role'), 'redirect' => $redirect]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Logged out']);
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', 'min:8', Password::defaults()],
            'role' => ['required', 'in:creator,brand'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        Auth::login($user);

        $redirect = $user->role === 'creator' ? url('/creator/choose-plan') : url('/brand/choose-plan');
        return response()->json(['user' => $user->only('id', 'name', 'email', 'role'), 'redirect' => $redirect]);
    }
}
