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
use App\Notifications\VerifyEmailOtpNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
        $user = $request->user();
        if (! $user->hasVerifiedEmail()) {
            Auth::logout();
            $request->session()->invalidate();
            $this->sendOtpToUser($user);
            return response()->json([
                'message' => 'Email not verified. We have sent an OTP to your email.',
                'needs_verification' => true,
                'email' => $user->email,
                'redirect' => url('/verify-email'),
            ], 422);
        }
        $request->session()->regenerate();
        $request->session()->save();
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
        try {
            Auth::guard('web')->logout();
            $request->session()->flush();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } catch (\Throwable $e) {
            report($e);
        }
        try {
            $request->session()->save();
        } catch (\Throwable $e) {
            report($e);
        }

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
        $this->sendOtpToUser($user);
        return response()->json([
            'message' => 'Please verify your email with the OTP we sent.',
            'needs_verification' => true,
            'email' => $user->email,
            'redirect' => url('/verify-email'),
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
        $this->sendOtpToUser($user);
        return response()->json([
            'message' => 'Please verify your email with the OTP we sent.',
            'needs_verification' => true,
            'email' => $user->email,
            'redirect' => url('/verify-email'),
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
        $this->sendOtpToUser($user);
        return response()->json([
            'message' => 'Please verify your email with the OTP we sent.',
            'needs_verification' => true,
            'email' => $user->email,
            'redirect' => url('/verify-email'),
        ], 201);
    }

    /**
     * Register as Studio Owner: user + role (no extra profile table).
     */
    public function registerStudioOwner(RegisterStudioOwnerRequest $request): JsonResponse
    {
        $user = $this->createUserWithRole($request->only('name', 'email', 'password'), 'studio_owner');
        $this->sendOtpToUser($user);
        return response()->json([
            'message' => 'Please verify your email with the OTP we sent.',
            'needs_verification' => true,
            'email' => $user->email,
            'redirect' => url('/verify-email'),
        ], 201);
    }

    /**
     * Register as Customer: user + role (for exploring & booking studios).
     */
    public function registerCustomer(RegisterCustomerRequest $request): JsonResponse
    {
        $user = $this->createUserWithRole($request->only('name', 'email', 'password'), 'customer');
        $this->sendOtpToUser($user);
        return response()->json([
            'message' => 'Please verify your email with the OTP we sent.',
            'needs_verification' => true,
            'email' => $user->email,
            'redirect' => url('/verify-email'),
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

    /**
     * Verify email with OTP (sent after email registration). On success, log user in and return redirect.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'otp' => ['required', 'string', 'size:6'],
        ]);
        $email = $request->email;
        $otp = $request->otp;
        $key = 'email_otp:' . $email;
        $stored = Cache::get($key);
        if ($stored === null || (string) $stored !== (string) $otp) {
            throw ValidationException::withMessages(['otp' => ['Invalid or expired verification code.']]);
        }
        $user = User::where('email', $email)->first();
        if (! $user) {
            throw ValidationException::withMessages(['email' => ['User not found.']]);
        }
        $user->markEmailAsVerified();
        Cache::forget($key);
        Auth::login($user, true);
        $request->session()->regenerate();
        $request->session()->save();
        $redirect = $this->postVerificationRedirect($user);

        return response()->json([
            'message' => 'Email verified.',
            'verified' => true,
            'user' => $this->userPayload($user),
            'redirect' => $redirect,
        ]);
    }

    /**
     * Resend OTP to email (for unverified users).
     */
    public function resendOtp(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'string', 'email']]);
        $user = User::where('email', $request->email)->first();
        if (! $user || $user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Invalid or already verified.'], 422);
        }
        $this->sendOtpToUser($user);

        return response()->json(['message' => 'Verification code sent to your email.']);
    }

    private function sendOtpToUser(User $user): void
    {
        $otp = (string) random_int(100000, 999999);
        Cache::put('email_otp:' . $user->email, $otp, 600); // 10 minutes
        $user->notify(new VerifyEmailOtpNotification($otp));
    }

    private function postVerificationRedirect(User $user): string
    {
        $primary = $user->primaryRole();
        $slug = $primary?->slug ?? 'customer';

        return match ($slug) {
            'admin' => url('/admin'),
            'creator' => url('/creator/choose-plan'),
            'brand' => url('/brand/choose-plan'),
            'agency' => url('/agency/dashboard'),
            'studio_owner' => url('/studio/dashboard'),
            default => url('/'),
        };
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
