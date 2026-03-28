<?php

namespace App\Services;

use App\Models\SocialAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialAnalyticsService
{
    /**
     * Update social account statistics from provider.
     */
    public function updateStats(SocialAccount $account): bool
    {
        if (! $account->access_token) {
            return false;
        }

        return match ($account->platform) {
            'youtube' => $this->updateYouTubeStats($account),
            'facebook' => $this->updateFacebookStats($account),
            'instagram' => $this->updateInstagramStats($account),
            default => false,
        };
    }

    private function updateYouTubeStats(SocialAccount $account): bool
    {
        try {
            $res = Http::withToken($account->access_token)
                ->get('https://www.googleapis.com/youtube/v3/channels', [
                    'mine' => 'true',
                    'part' => 'statistics,snippet',
                ]);

            if ($res->successful()) {
                $item = $res->json('items.0');
                Log::debug('YouTube API Response Item:', ['item' => $item]);
                if ($item) {
                    $account->followers_count = $item['statistics']['subscriberCount'] ?? $account->followers_count;
                    $account->username = $item['snippet']['customUrl'] ?? $item['snippet']['title'] ?? $account->username;
                    Log::info("YouTube stats updated for account {$account->id}", [
                        'followers' => $account->followers_count,
                        'username' => $account->username
                    ]);
                    $account->save();
                    
                    // Also fetch historical analytics for graphs
                    $this->updateYouTubeAnalytics($account);
                    
                    return true;
                } else {
                    Log::warning("YouTube API returned no items for account {$account->id}");
                }
            } else {
                Log::error("YouTube API error for account {$account->id}", [
                    'status' => $res->status(),
                    'body' => $res->body()
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('YouTube Analytics update failed: ' . $e->getMessage());
        }

        return false;
    }

    private function updateYouTubeAnalytics(SocialAccount $account)
    {
        try {
            $end = now()->toDateString();
            $start = now()->subDays(60)->toDateString(); // 60 days for better demographic data
            
            // 1. Fetch Growth History (30 days)
            $growthRes = Http::withToken($account->access_token)
                ->get('https://youtubeanalytics.googleapis.com/v2/reports', [
                    'ids' => 'channel==MINE',
                    'startDate' => now()->subDays(30)->toDateString(),
                    'endDate' => $end,
                    'metrics' => 'subscribersGained,subscribersLost,views,likes',
                    'dimensions' => 'day',
                    'sort' => 'day',
                ]);

            // 2. Fetch Audience Demographics (Gender & Age)
            $demoRes = Http::withToken($account->access_token)
                ->get('https://youtubeanalytics.googleapis.com/v2/reports', [
                    'ids' => 'channel==MINE',
                    'startDate' => $start,
                    'endDate' => $end,
                    'metrics' => 'viewerPercentage',
                    'dimensions' => 'ageGroup,gender',
                    'sort' => 'ageGroup,gender',
                ]);

            // 3. Fetch Top Content (Videos)
            $topVideosRes = Http::withToken($account->access_token)
                ->get('https://youtubeanalytics.googleapis.com/v2/reports', [
                    'ids' => 'channel==MINE',
                    'startDate' => $start,
                    'endDate' => $end,
                    'metrics' => 'views,estimatedMinutesWatched,averageViewDuration',
                    'dimensions' => 'video',
                    'sort' => '-views',
                    'maxResults' => 5
                ]);

            // 4. Fetch Video Details (Titles/Thumbnails) for the top videos
            $topVideos = [];
            if ($topVideosRes->successful() && !empty($topVideosRes->json('rows'))) {
                $videoIds = collect($topVideosRes->json('rows'))->pluck(0)->implode(',');
                $detailsRes = Http::withToken($account->access_token)
                    ->get('https://www.googleapis.com/youtube/v3/videos', [
                        'id' => $videoIds,
                        'part' => 'snippet,statistics',
                    ]);
                
                if ($detailsRes->successful()) {
                    $topVideos = collect($detailsRes->json('items'))->map(function($v) {
                        return [
                            'id' => $v['id'],
                            'title' => $v['snippet']['title'],
                            'thumbnail' => $v['snippet']['thumbnails']['medium']['url'],
                            'views' => $v['statistics']['viewCount'] ?? 0,
                        ];
                    })->toArray();
                }
            }

            if ($growthRes->successful() || $demoRes->successful()) {
                $account->analytics_data = array_merge((array)$account->analytics_data, [
                    'last_updated' => now()->toIso8601String(),
                    'history' => $growthRes->json('rows') ?? [],
                    'demographics' => $demoRes->json('rows') ?? [],
                    'top_videos' => $topVideos,
                ]);
                $account->save();
                Log::info("YouTube expanded analytics updated for account {$account->id}");
            }
        } catch (\Throwable $e) {
            Log::error("Failed to fetch YouTube analytics: " . $e->getMessage());
        }
    }

    private function updateFacebookStats(SocialAccount $account): bool
    {
        try {
            // Fetch FB profile stats (followers count for pages/profiles if accessible)
            $res = Http::withToken($account->access_token)
                ->get('https://graph.facebook.com/v19.0/me', [
                    'fields' => 'name,followers_count',
                ]);

            if ($res->successful()) {
                $data = $res->json();
                $account->username = $data['name'] ?? $account->username;
                $account->followers_count = $data['followers_count'] ?? $account->followers_count;
                $account->save();
                return true;
            }
        } catch (\Throwable $e) {
            Log::error('Facebook Analytics update failed: ' . $e->getMessage());
        }

        return false;
    }

    private function updateInstagramStats(SocialAccount $account): bool
    {
        try {
            // Usually connected via Facebook. Need to find the linked IG business account.
            $res = Http::withToken($account->access_token)
                ->get('https://graph.facebook.com/v19.0/me/accounts', [
                    'fields' => 'instagram_business_account{id,username,followers_count,profile_picture_url}',
                ]);

            if ($res->successful()) {
                $pages = $res->json('data', []);
                foreach ($pages as $page) {
                    $ig = $page['instagram_business_account'] ?? null;
                    if ($ig) {
                        $account->username = $ig['username'] ?? $account->username;
                        $account->followers_count = $ig['followers_count'] ?? $account->followers_count;
                        $account->save();
                        return true;
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Instagram Analytics update failed: ' . $e->getMessage());
        }

        return false;
    }
}
