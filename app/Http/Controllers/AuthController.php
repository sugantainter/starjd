<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterAgencyRequest;
use App\Http\Requests\Auth\RegisterBrandRequest;
use App\Http\Requests\Auth\RegisterCreatorRequest;
use App\Http\Requests\Auth\RegisterCustomerRequest;
use App\Http\Requests\Auth\RegisterStudioOwnerRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\Agency;
use App\Models\BrandProfile;
use App\Models\CreatorProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login (RBAC: checks primary role; optional ?role= for account-type hint).
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        $request->session()->save();

        $user = $request->user();
        $redirect = $this->redirectForUser($user);

        return response()->json([
            'user' => $this->userPayload($user),
            'redirect' => $redirect,
        ]);
    }

    /**
     * Current user with roles (for /api/me).
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        return response()->json($this->userPayload($user->load('roles')));
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out']);
    }

    /**
     * Register as Creator: user + creator_profile + role.
     */
    public function registerCreator(RegisterCreatorRequest|Request $request): JsonResponse
    {
        if ($request instanceof Request && ! $request instanceof RegisterCreatorRequest) {
            $request->validate((new RegisterCreatorRequest)->rules());
        }
        $user = $this->createUserWithRole($request->only('name', 'email', 'password'), 'creator');
        CreatorProfile::create(['user_id' => $user->id]);
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->save();

        return response()->json([
            'user' => $this->userPayload($user),
            'redirect' => url('/creator/choose-plan'),
        ], 201);
    }

    /**
     * Register as Brand: user + brand_profile + role.
     */
    public function registerBrand(RegisterBrandRequest|Request $request): JsonResponse
    {
        if ($request instanceof Request && ! $request instanceof RegisterBrandRequest) {
            $request->validate((new RegisterBrandRequest)->rules());
        }
        $user = $this->createUserWithRole($request->only('name', 'email', 'password'), 'brand');
        BrandProfile::create(['user_id' => $user->id]);
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->save();

        return response()->json([
            'user' => $this->userPayload($user),
            'redirect' => url('/brand/choose-plan'),
        ], 201);
    }

    /**
     * Register as Agency: user + agency record + role.
     */
    public function registerAgency(RegisterAgencyRequest $request): JsonResponse
    {
        $user = $this->createUserWithRole(
            $request->only('name', 'email', 'password'),
            'agency'
        );
        Agency::create([
            'user_id' => $user->id,
            'company_name' => $request->company_name,
            'gst_number' => $request->gst_number,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'website' => $request->website,
            'approval_status' => 'pending',
        ]);
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->save();

        return response()->json([
            'user' => $this->userPayload($user),
            'redirect' => url('/agency/dashboard'),
        ], 201);
    }

    /**
     * Register as Studio Owner: user + role (no extra profile table).
     */
    public function registerStudioOwner(RegisterStudioOwnerRequest $request): JsonResponse
    {
        $user = $this->createUserWithRole($request->only('name', 'email', 'password'), 'studio_owner');
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->save();

        return response()->json([
            'user' => $this->userPayload($user),
            'redirect' => url('/studio/dashboard'),
        ], 201);
    }

    /**
     * Register as Customer: user + role (for exploring & booking studios).
     */
    public function registerCustomer(RegisterCustomerRequest $request): JsonResponse
    {
        $user = $this->createUserWithRole($request->only('name', 'email', 'password'), 'customer');
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->save();

        return response()->json([
            'user' => $this->userPayload($user),
            'redirect' => url('/studios'),
        ], 201);
    }

    /**
     * Legacy register: accepts role creator|brand; redirects to specific handler.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', 'min:8', \Illuminate\Validation\Rules\Password::defaults()],
            'role' => ['required', 'in:creator,brand'],
        ]);

        return $request->role === 'creator'
            ? $this->registerCreator($request)
            : $this->registerBrand($request);
    }

    /**
     * Forgot password: send reset link.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->json(['message' => __($status)]);
    }

    /**
     * Reset password with token.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->json(['message' => __('Password has been reset.')]);
    }

    /**
     * Resend email verification. When authenticated, uses current user; otherwise requires email.
     */
    public function resendVerification(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            if ($user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Already verified.'], 422);
            }
            $user->sendEmailVerificationNotification();
            return response()->json(['message' => 'Verification link sent.']);
        }

        $request->validate(['email' => ['required', 'string', 'email']]);
        $user = User::where('email', $request->email)->first();
        if (! $user || $user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Invalid or already verified.'], 422);
        }
        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent.']);
    }

    private function createUserWithRole(array $data, string $roleSlug): User
    {
        return DB::transaction(function () use ($data, $roleSlug) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $role = Role::where('slug', $roleSlug)->first();
            if ($role) {
                $user->roles()->attach($role->id, ['is_primary' => true]);
            }

            return $user;
        });
    }

    private function redirectForUser(User $user): string
    {
        $primary = $user->primaryRole();
        $slug = $primary?->slug ?? 'customer';

        return match ($slug) {
            'admin' => url('/admin'),
            'creator' => url('/creator/dashboard'),
            'brand' => url('/brand/dashboard'),
            'agency' => url('/agency/dashboard'),
            'studio_owner' => url('/studio/dashboard'),
            default => url('/'),
        };
    }

    private function userPayload(User $user): array
    {
        $primary = $user->primaryRole();
        $roles = $user->roles()->get()->map(fn ($r) => ['id' => $r->id, 'name' => $r->name, 'slug' => $r->slug]);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at?->toIso8601String(),
            'role' => $user->role,
            'primary_role' => $primary ? ['id' => $primary->id, 'name' => $primary->name, 'slug' => $primary->slug] : null,
            'roles' => $roles,
        ];
    }
}
