<?php

namespace App\Http\Controllers;

use App\Models\BrandProfile;
use App\Models\CreatorProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth. Optional query: role=creator|brand (for new users).
     */
    public function googleRedirect(Request $request): RedirectResponse
    {
        $this->storeOAuthRole($request);
        $redirectUri = config('services.google.redirect') ?: url('/auth/google/callback');
        return Socialite::driver('google')
            ->redirectUrl($redirectUri)
            ->redirect();
    }

    /**
     * Handle Google OAuth callback: login or register user, then redirect to SPA.
     */
    public function googleCallback(Request $request): RedirectResponse
    {
        try {
            $oauthUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->to('/login?error=google');
        }
        return $this->handleOAuthUser($request, $oauthUser);
    }

    /**
     * Redirect to Facebook OAuth. Optional query: role=creator|brand (for new users).
     */
    public function facebookRedirect(Request $request): RedirectResponse
    {
        $this->storeOAuthRole($request);
        $redirectUri = config('services.facebook.redirect') ?: url('/auth/facebook/callback');
        return Socialite::driver('facebook')
            ->redirectUrl($redirectUri)
            ->scopes(['email', 'public_profile'])
            ->redirect();
    }

    /**
     * Handle Facebook OAuth callback: login or register user, then redirect to SPA.
     */
    public function facebookCallback(Request $request): RedirectResponse
    {
        try {
            $oauthUser = Socialite::driver('facebook')->user();
        } catch (\Throwable $e) {
            return redirect()->to('/login?error=facebook');
        }
        return $this->handleOAuthUser($request, $oauthUser);
    }

    private function storeOAuthRole(Request $request): void
    {
        $role = $request->query('role');
        $allowed = ['creator', 'brand', 'studio_owner', 'agency', 'customer'];
        if (in_array($role, $allowed, true)) {
            $request->session()->put('oauth_role', $role);
        }
    }

    private function handleOAuthUser(Request $request, SocialiteUser $oauthUser): RedirectResponse
    {
        $role = $request->session()->pull('oauth_role', 'customer');

        $email = $oauthUser->getEmail();
        if (! $email) {
            return redirect()->to('/login?error=no_email');
        }

        $user = User::where('email', $email)->first();
        if ($user) {
            $request->session()->forget('oauth_register');
            Auth::login($user, true);
            $request->session()->regenerate();
            $primary = $user->primaryRole();
            $slug = $primary?->slug ?? 'customer';
            $redirect = match ($slug) {
                'admin' => url('/admin'),
                'creator' => url('/creator/dashboard'),
                'brand' => url('/brand/dashboard'),
                'agency' => url('/agency/dashboard'),
                'studio_owner' => url('/studio/dashboard'),
                default => url('/'),
            };
            return redirect()->away($redirect);
        }

        $request->session()->put('oauth_register', true);
        $name = $oauthUser->getName() ?: explode('@', $email)[0];
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32)),
        ]);
        $user->markEmailAsVerified(); // OAuth (Google/Facebook) emails are auto-verified
        $roleModel = Role::where('slug', $role)->first();
        if ($roleModel) {
            $user->roles()->attach($roleModel->id, ['is_primary' => true]);
        }
        if ($role === 'creator') {
            CreatorProfile::create(['user_id' => $user->id]);
        }
        if ($role === 'brand') {
            BrandProfile::create(['user_id' => $user->id]);
        }
        Auth::login($user, true);
        $request->session()->regenerate();
        $redirect = match ($role) {
            'creator' => url('/creator/choose-plan'),
            'brand' => url('/brand/choose-plan'),
            'studio_owner' => url('/studio/dashboard'),
            'agency' => url('/agency/dashboard'),
            default => url('/'),
        };
        return redirect()->away($redirect);
    }
}
