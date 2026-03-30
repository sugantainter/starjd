<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreatorSocialAccountController extends Controller
{
    public static function platforms(): array
    {
        $platforms = config('creator.platforms', []);
        return array_keys($platforms);
    }

    public function index(Request $request): JsonResponse
    {
        $accounts = $request->user()->socialAccounts;
        $platformKeys = self::platforms();
        $defaults = collect($platformKeys)->map(fn ($p) => [
            'platform' => $p,
            'username' => null,
            'profile_url' => null,
            'followers_count' => null,
            'is_connected' => false,
        ]);
        foreach ($accounts as $acc) {
            $defaults->transform(function ($item) use ($acc) {
                if ($item['platform'] === $acc->platform) {
                    return [
                        'platform' => $acc->platform,
                        'username' => $acc->username,
                        'profile_url' => $acc->profile_url,
                        'followers_count' => $acc->followers_count,
                        'is_connected' => $acc->is_connected,
                        'id' => $acc->id,
                    ];
                }
                return $item;
            });
        }
        return response()->json($defaults->values());
    }

    public function sync(Request $request): JsonResponse
    {
        $request->validate([
            'platform' => ['required', 'string', 'in:' . implode(',', self::platforms())],
            'username' => ['nullable', 'string', 'max:255'],
            'profile_url' => ['nullable', 'string', 'url', 'max:500'],
            'followers_count' => ['nullable', 'integer', 'min:0'],
        ]);

        $user = $request->user();
        $account = $user->socialAccounts()->firstOrNew(['platform' => $request->platform]);
        $account->username = $request->username;
        $account->profile_url = $request->profile_url;
        $account->followers_count = $request->followers_count;
        $account->is_connected = (bool) ($request->username || $request->profile_url);
        $account->save();

        return response()->json($account);
    }

    public function redirect(Request $request, string $platform)
    {
        $allowed = ['facebook', 'google', 'linkedin']; // google is used for YouTube
        if (! in_array($platform, $allowed, true)) {
            return redirect()->to('/creator/social-accounts?error=platform_not_supported');
        }

        // Force the redirect URL for this specific connection flow
        $callbackUrl = route('creator.social.callback', ['platform' => $platform]);
        config(["services.{$platform}.redirect" => $callbackUrl]);

        if ($platform === 'google') {
            return Socialite::driver('google')
                ->scopes(['yt-analytics.readonly', 'https://www.googleapis.com/auth/youtube.readonly'])
                ->with(['access_type' => 'offline', 'prompt' => 'consent'])
                ->redirect();
        }

        if ($platform === 'facebook') {
            /** @var \Laravel\Socialite\Two\FacebookProvider $driver */
            $driver = Socialite::driver('facebook');
            
            $builder = $driver->scopes([
                'email',
                'public_profile',
                'pages_show_list',
                'pages_read_engagement',
                'instagram_basic',
                'instagram_manage_insights',
                'business_management',
            ]);

            return $builder->redirect();
        }

        if ($platform === 'linkedin') {
            return Socialite::driver('linkedin-openid')
                ->setScopes(['openid', 'profile', 'email', 'w_member_social'])
                ->redirect();
        }

        return Socialite::driver($platform)->redirect();
    }

    public function callback(Request $request, string $platform, \App\Services\SocialAnalyticsService $analytics)
    {
        // Must match the redirect URL used in redirect()
        $callbackUrl = route('creator.social.callback', ['platform' => $platform]);
        config(["services.{$platform}.redirect" => $callbackUrl]);

        try {
            $driverName = ($platform === 'linkedin') ? 'linkedin-openid' : $platform;
            $oauthUser = Socialite::driver($driverName)->user();
        } catch (\Throwable $e) {
            return redirect()->to('/creator/social-accounts?error=oauth_failed&msg=' . urlencode($e->getMessage()));
        }

        $user = $request->user();
        
        // Map google to youtube for storage
        $savePlatform = ($platform === 'google') ? 'youtube' : $platform;

        $account = $user->socialAccounts()->firstOrNew(['platform' => $savePlatform]);
        $account->access_token = $oauthUser->token;
        $account->refresh_token = $oauthUser->refreshToken;
        $account->expires_at = $oauthUser->expiresIn ? now()->addSeconds($oauthUser->expiresIn) : null;
        $account->is_connected = true;

        // Fetch initial stats
        $analytics->updateStats($account);

        $account->save();

        return redirect()->to('/creator/social-accounts?success=connected');
    }

    public function refresh(Request $request, string $platform, \App\Services\SocialAnalyticsService $analytics): JsonResponse
    {
        $account = $request->user()->socialAccounts()->where('platform', $platform)->first();
        if (! $account || ! $account->access_token) {
            return response()->json(['message' => 'Account not connected via OAuth'], 422);
        }

        $success = $analytics->updateStats($account);
        return response()->json([
            'success' => $success,
            'followers_count' => $account->followers_count,
            'username' => $account->username,
        ]);
    }

    public function selectPage(Request $request, string $platform, \App\Services\SocialAnalyticsService $analytics): JsonResponse
    {
        $account = $request->user()->socialAccounts()->where('platform', 'facebook')->first();
        if (! $account) {
            return response()->json(['message' => 'Facebook not connected'], 422);
        }

        $pageId = $request->input('page_id');
        if (! $pageId) {
            return response()->json(['message' => 'Page ID required'], 422);
        }

        // Update the account's selected page ID in metadata
        $data = (array) $account->analytics_data;
        $data['fb_page_id'] = $pageId;
        $account->analytics_data = $data;
        $account->save();

        // Re-sync stats for this specific page
        $success = $analytics->updateStats($account);

        return response()->json([
            'success' => $success,
            'page_id' => $pageId,
            'followers_count' => $account->followers_count,
            'username' => $account->username,
        ]);
    }

    public function disconnect(Request $request, string $platform): JsonResponse
    {
        if (! in_array($platform, self::platforms(), true)) {
            return response()->json(['message' => 'Invalid platform'], 422);
        }
        $request->user()->socialAccounts()->where('platform', $platform)->delete();
        return response()->json(['message' => 'Disconnected']);
    }
}
