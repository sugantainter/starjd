<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
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

    public function disconnect(Request $request, string $platform): JsonResponse
    {
        if (! in_array($platform, self::platforms(), true)) {
            return response()->json(['message' => 'Invalid platform'], 422);
        }
        $request->user()->socialAccounts()->where('platform', $platform)->delete();
        return response()->json(['message' => 'Disconnected']);
    }
}
