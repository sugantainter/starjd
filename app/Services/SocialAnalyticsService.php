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
            'linkedin' => $this->updateLinkedInStats($account),
            'pinterest' => $this->updatePinterestStats($account),
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
                Log::info("YouTube analytics payload for account {$account->id}", [
                    'historyCount' => count($growthRes->json('rows') ?? []),
                    'demoCount' => count($demoRes->json('rows') ?? []),
                    'topVideosCount' => count($topVideos)
                ]);
                $account->analytics_data = array_merge((array)$account->analytics_data, [
                    'last_updated' => now()->toIso8601String(),
                    'history' => $growthRes->json('rows') ?? [],
                    'demographics' => $demoRes->json('rows') ?? [],
                    'top_videos' => $topVideos,
                ]);
                $account->save();
                Log::info("YouTube expanded analytics successfully persisted for account {$account->id}");
            } else {
                Log::warning("YouTube analytics fetch returned partial success for account {$account->id}", [
                    'growth' => $growthRes->status(),
                    'demo' => $demoRes->status()
                ]);
            }
        } catch (\Throwable $e) {
            Log::error("Failed to fetch YouTube analytics: " . $e->getMessage());
        }
    }

    private function updateFacebookStats(SocialAccount $account): bool
    {
        try {
            Log::info("Starting Facebook sync for account {$account->id}");
            // 1. Fetch FB User Stats (Limited fields to avoid #100 error)
            $res = Http::withToken($account->access_token)
                ->get('https://graph.facebook.com/v19.0/me', [
                    'fields' => 'name,picture',
                ]);
            
            // Check Permissions
            $permRes = Http::withToken($account->access_token)->get('https://graph.facebook.com/v19.0/me/permissions');
            if ($permRes->successful()) {
                $granted = collect($permRes->json('data'))->where('status', 'granted')->pluck('permission')->implode(', ');
                Log::info("OAuth Permissions Audit for account {$account->id}: [{$granted}]");
            }

            if ($res->successful()) {
                $data = $res->json();
                $account->username = $data['name'] ?? $account->username;
                $account->followers_count = $data['followers_count'] ?? $account->followers_count;
                $account->save();
                Log::info("Found Facebook User: {$account->username}");
                
                // 2. Discover and update Facebook Pages
                $pagesRes = Http::withToken($account->access_token)
                    ->get('https://graph.facebook.com/v19.0/me/accounts', [
                        'fields' => 'id,name,username,followers_count,access_token,instagram_business_account',
                    ]);
                
                if ($pagesRes->successful() && !empty($pagesRes->json('data'))) {
                    $pageList = collect($pagesRes->json('data'));
                    Log::info("Discovered " . $pageList->count() . " Facebook Page(s)");
                    
                    // Smart Selection: Prioritize page with Linked Instagram, then by Follower count
                    $page = $pageList->sortByDesc(function ($p) {
                        $pWeight = isset($p['instagram_business_account']) ? 1000000000 : 0;
                        return $pWeight + ($p['followers_count'] ?? 0);
                    })->first();

                    $pageId = $page['id'];
                    $pageToken = $page['access_token'];
                    $account->followers_count = $page['followers_count'] ?? $account->followers_count;
                    Log::info("Intelligent Sync Selected Page: {$page['name']} (ID: {$pageId}) with {$account->followers_count} followers. Linked to IG: " . (isset($page['instagram_business_account']) ? 'Yes' : 'No'));

                    // 3. Fetch Page Insights (Last 30 days) - only if they have enough followers for metrics
                    $insightsRes = null;
                    if ($account->followers_count >= 10) {
                        Log::info("Fetching isolated insights for Page {$pageId}...");
                        // 1. Reach (Independently) - try modern first, then legacy
                        $reachRes = Http::withToken($pageToken)->get("https://graph.facebook.com/v19.0/{$pageId}/insights", [
                            'metric' => 'page_impressions_unique', // Try legacy reach for wider compatibility
                            'period' => 'day',
                            'since' => now()->subDays(30)->timestamp,
                            'until' => now()->timestamp,
                        ]);
                        
                        if (!$reachRes->successful()) {
                           Log::warning("Legacy Reach failed, trying modern page_reach...");
                           $reachRes = Http::withToken($pageToken)->get("https://graph.facebook.com/v19.0/{$pageId}/insights", [
                                'metric' => 'page_reach',
                                'period' => 'day',
                                'since' => now()->subDays(30)->timestamp,
                                'until' => now()->timestamp,
                            ]);
                        }
                        if (!$reachRes->successful()) Log::warning("FB Page Reach Fetch Error: " . $reachRes->body());

                        // 2. Engagement (Independently) - try legacy first for widespread support
                        $engageRes = Http::withToken($pageToken)->get("https://graph.facebook.com/v19.0/{$pageId}/insights", [
                            'metric' => 'page_post_engagements',
                            'period' => 'day',
                            'since' => now()->subDays(30)->timestamp,
                            'until' => now()->timestamp,
                        ]);
                        if (!$engageRes->successful()) {
                            Log::warning("Legacy Engagement failed, trying modern page_engaged_users...");
                            $engageRes = Http::withToken($pageToken)->get("https://graph.facebook.com/v19.0/{$pageId}/insights", [
                                'metric' => 'page_engaged_users',
                                'period' => 'day',
                                'since' => now()->subDays(30)->timestamp,
                                'until' => now()->timestamp,
                            ]);
                        }
                        if (!$engageRes->successful()) Log::warning("FB Page Engagement Fetch Error: " . $engageRes->body());

                        // 3. Views (Independently)
                        $viewsRes = Http::withToken($pageToken)->get("https://graph.facebook.com/v19.0/{$pageId}/insights", [
                            'metric' => 'page_views_total',
                            'period' => 'day',
                            'since' => now()->subDays(30)->timestamp,
                            'until' => now()->timestamp,
                        ]);
                        if (!$viewsRes->successful()) Log::warning("FB Page Views Fetch Error: " . $viewsRes->body());
                    }

                    // 4. Fetch Page Audience Demographics (Requires > 100 followers)
                    $demoRes = null;
                    if ($account->followers_count >= 100) {
                        $demoRes = Http::withToken($pageToken)
                            ->get("https://graph.facebook.com/v19.0/{$pageId}/insights", [
                                'metric' => 'page_fans_gender_age',
                                'period' => 'lifetime',
                            ]);
                    }

                    // 5. Fetch Top Page Posts
                    $postsRes = Http::withToken($pageToken)
                        ->get("https://graph.facebook.com/v19.0/{$pageId}/posts", [
                            'fields' => 'id,message,attachments{media},likes.summary(true),comments.summary(true),created_time',
                            'limit' => 5,
                        ]);

                    // Prepare individual datasets
                    $formattedDemo = [];
                    if ($demoRes && $demoRes->successful() && !empty($demoRes->json('data.0.values.0.value'))) {
                        $values = $demoRes->json('data.0.values.0.value');
                        $total = array_sum($values);
                        foreach ($values as $key => $count) {
                            if (!str_contains($key, '.')) continue;
                            [$genderCode, $age] = explode('.', $key);
                            $gender = ($genderCode === 'M') ? 'male' : 'female';
                            $formattedDemo[] = ['age' . $age, $gender, ($total > 0 ? ($count / $total) * 100 : 0)];
                        }
                    }

                    $history = [];
                    if (($reachRes && $reachRes->successful()) || ($engageRes && $engageRes->successful()) || ($viewsRes && $viewsRes->successful())) {
                        // Pull Reach as primary growth metric
                        $reachData = $reachRes && $reachRes->successful() ? ($reachRes->json('data.0.values') ?? []) : [];
                        foreach ($reachData as $val) {
                            $history[] = [
                                date('Y-m-d', strtotime($val['end_time'])),
                                0, 0, $val['value'],
                            ];
                        }
                    }

                    // Always save basic info and discovered pages list even if metrics fail
                    $account->analytics_data = array_merge((array)$account->analytics_data, [
                        'fb_page_id' => $pageId,
                        'discovered_pages' => $pageList->map(fn($p) => [
                            'id' => $p['id'],
                            'name' => $p['name'],
                            'followers' => $p['followers_count'] ?? 0,
                            'has_ig' => isset($p['instagram_business_account'])
                        ])->toArray(),
                        'last_updated' => now()->toIso8601String(),
                        'history' => !empty($history) ? $history : ($account->analytics_data['history'] ?? []),
                        'demographics' => !empty($formattedDemo) ? $formattedDemo : ($account->analytics_data['demographics'] ?? []),
                        'top_videos' => collect($postsRes->json('data', []))->map(fn($p) => [
                            'id' => $p['id'],
                            'title' => $p['message'] ?? 'Facebook Post',
                            'thumbnail' => $p['attachments']['data'][0]['media']['image']['src'] ?? null,
                            'views' => ($p['likes']['summary']['total_count'] ?? 0) + ($p['comments']['summary']['total_count'] ?? 0),
                        ])->toArray(),
                    ]);
                    
                    $account->save();
                    Log::info("Facebook account {$account->id} data persisted (Pages: " . count($account->analytics_data['discovered_pages']) . ")");

                    if (!$reachRes || !$reachRes->successful()) {
                        Log::warning("Facebook Page reach fetch failed/skipped for ID {$pageId}");
                    }
                } else {
                    Log::warning("No Facebook Pages discovered for account {$account->id}");
                }

                // Also trigger linked Instagram update 
                $this->updateInstagramStats($account);
                
                return true;
            } else {
                Log::error("Failed to fetch basic FB profile for account {$account->id}", [
                    'status' => $res->status(),
                    'body' => $res->body()
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Facebook Page Analytics update traceback: ' . $e->getMessage());
        }

        return false;
    }

    private function updateInstagramStats(SocialAccount $account): bool
    {
        try {
            Log::info("Starting Instagram discovery for account {$account->id}");
            // 1. Find Instagram Business Account via FB Pages
            $pagesRes = Http::withToken($account->access_token)
                ->get('https://graph.facebook.com/v19.0/me/accounts', [
                    'fields' => 'instagram_business_account{id,username,followers_count,media_count,profile_picture_url}',
                ]);

            if ($pagesRes->successful()) {
                $pages = $pagesRes->json('data', []);
                $discoveredIgs = [];
                foreach ($pages as $p) {
                    if (isset($p['instagram_business_account'])) {
                        $discoveredIgs[] = [
                            'id' => $p['instagram_business_account']['id'],
                            'username' => $p['instagram_business_account']['username'],
                            'followers' => $p['instagram_business_account']['followers_count'] ?? 0,
                        ];
                    }
                }
                
                // Store discovery bank
                $account->analytics_data = array_merge((array)$account->analytics_data, [
                     'discovered_instagrams' => $discoveredIgs,
                ]);
                $account->save();

                // If no manual selection exists, pick the first one by followers
                $selectedIgId = $account->analytics_data['ig_id'] ?? (collect($discoveredIgs)->sortByDesc('followers')->first()['id'] ?? null);

                if ($selectedIgId) {
                    return $this->performInstagramSync($account, $selectedIgId);
                }
                Log::warning("No IG Business accounts linked to any Facebook Page for account {$account->id}");
                return false;
            } else {
                Log::error("Failed to fetch FB Pages for IG discovery: {$pagesRes->body()}");
            }
        } catch (\Throwable $e) {
            Log::error('Instagram discovery process failed: ' . $e->getMessage());
        }

        return false;
    }

    private function performInstagramSync(SocialAccount $account, string $igId): bool
    {
        try {
            Log::info("Performing IG sync for IG ID: {$igId}");

            // 1. Fetch IG Reach/Impressions (Last 30 days)
            $insightsRes = Http::withToken($account->access_token)
                ->get("https://graph.facebook.com/v19.0/{$igId}/insights", [
                    'metric' => 'reach',
                    'period' => 'day',
                    'since' => now()->subDays(30)->timestamp,
                    'until' => now()->timestamp,
                ]);

            // 2. Fetch IG Audience Demographics
            $demoRes = Http::withToken($account->access_token)
                ->get("https://graph.facebook.com/v19.0/{$igId}/insights", [
                    'metric' => 'audience_gender_age',
                    'period' => 'lifetime',
                ]);

            // 3. Fetch Top Media
            $mediaRes = Http::withToken($account->access_token)
                ->get("https://graph.facebook.com/v19.0/{$igId}/media", [
                    'fields' => 'id,caption,media_type,media_url,thumbnail_url,like_count,comments_count,timestamp',
                    'limit' => 5,
                ]);

            // Format Demographics
            $formattedDemo = [];
            if ($demoRes->successful() && !empty($demoRes->json('data.0.values.0.value'))) {
                $values = $demoRes->json('data.0.values.0.value');
                $total = array_sum($values);
                foreach ($values as $key => $count) {
                    if (!str_contains($key, '.')) continue;
                    [$genderCode, $age] = explode('.', $key);
                    $gender = ($genderCode === 'M') ? 'male' : 'female';
                    $formattedDemo[] = ['age' . $age, $gender, ($total > 0 ? ($count / $total) * 100 : 0)];
                }
            }

            // Format History
            $history = [];
            if ($insightsRes->successful() && !empty($insightsRes->json('data'))) {
                $reachData = collect($insightsRes->json('data'))->firstWhere('name', 'reach')['values'] ?? [];
                foreach ($reachData as $val) {
                    $history[] = [
                        date('Y-m-d', strtotime($val['end_time'])),
                        0, 0, $val['value'],
                    ];
                }
            }

            // Auto-connect/update separate Instagram account
            $igAccount = \App\Models\SocialAccount::firstOrNew([
                'user_id' => $account->user_id,
                'platform' => 'instagram',
            ]);
            
            // Check if separate Instagram SocialAccount needs profile data
            if (!$igAccount->username || $igAccount->username === 'Instagram User') {
                $profileRes = Http::withToken($account->access_token)->get("https://graph.facebook.com/v19.0/{$igId}", ['fields' => 'username,followers_count']);
                $igAccount->username = $profileRes->json('username', 'Instagram User');
                $igAccount->followers_count = $profileRes->json('followers_count', 0);
            }
            
            $igAccount->access_token = $account->access_token;
            $igAccount->is_connected = true;
            $igAccount->analytics_data = array_merge((array)$igAccount->analytics_data, [
                'ig_id' => $igId,
                'last_updated' => now()->toIso8601String(),
                'history' => $history,
                'demographics' => $formattedDemo,
                'top_videos' => collect($mediaRes->json('data', []))->map(fn($m) => [
                    'id' => $m['id'],
                    'title' => $m['caption'] ?? 'Media Post',
                    'thumbnail' => $m['media_url'] ?? $m['thumbnail_url'] ?? null,
                    'views' => ($m['like_count'] ?? 0) + ($m['comments_count'] ?? 0),
                ])->toArray(),
            ]);
            $igAccount->save();
            
            // Update mapping id on primary fb account as well to persist selection
            $data = (array)$account->analytics_data;
            $data['ig_id'] = $igId;
            $account->analytics_data = $data;
            $account->save();
            
            Log::info("Instagram account card activated for ID {$igId}");
            return true;
        } catch (\Throwable $e) {
            Log::error('Instagram sync process failed: ' . $e->getMessage());
            return false;
        }
    }

    private function updateLinkedInStats(SocialAccount $account): bool
    {
        try {
            Log::info("Starting LinkedIn sync for account {$account->id}");
            // 1. Fetch Profile Info using modern OpenID endpoint
            $profileRes = Http::withToken($account->access_token)->get('https://api.linkedin.com/v2/userinfo');

            if ($profileRes->successful()) {
                $profile = $profileRes->json();
                $account->username = $profile['name'] ?? 'LinkedIn Member';
                $memberId = $profile['sub']; // Person ID in OpenID format

                // 2. Fetch Member Share Stats (Engagement)
                // Use the person ID from 'sub' to build the URN
                $statsRes = Http::withToken($account->access_token)
                    ->get('https://api.linkedin.com/v2/memberShareStatistics', [
                        'action' => 'getStatistics',
                        'ids' => "urn:li:person:{$memberId}",
                    ]);

                if ($statsRes->successful()) {
                    $json = $statsRes->json();
                    Log::info("LinkedIn Stat elements for {$account->id}: ", ['elements' => $json['elements'] ?? []]);
                    
                    $elements = $statsRes->json('elements.0');
                    $totalLikes = $elements['totalShareStatistics']['likeCount'] ?? 0;
                    $totalComments = $elements['totalShareStatistics']['commentCount'] ?? 0;
                    
                    // LinkedIn doesn't easily expose "Followers" without special API access, 
                    // so we use connection/engagement as a proxy if followers missing.
                    $account->followers_count = $account->followers_count ?? 0; 
                    
                    $history = [];
                    // Populate history with engagement data over 30 days
                    $history[] = [now()->toDateString(), $totalLikes, $totalComments, ($totalLikes + $totalComments)];

                    $account->analytics_data = array_merge((array)$account->analytics_data, [
                        'li_id' => $memberId,
                        'last_updated' => now()->toIso8601String(),
                        'history' => $history,
                        'total_likes' => $totalLikes,
                        'total_comments' => $totalComments,
                    ]);

                    Log::info("LinkedIn analytics data mapped for {$account->id}", [
                        'likes' => $totalLikes,
                        'comments' => $totalComments,
                        'memberId' => $memberId
                    ]);
                }
                
                $account->save();
                Log::info("LinkedIn stats successfully updated for account {$account->id}");
                return true;
            } else {
                Log::error("LinkedIn Profile fetch failed: {$profileRes->body()}");
            }
        } catch (\Throwable $e) {
            Log::error('LinkedIn analytics failed: ' . $e->getMessage());
        }

        return false;
    }

    private function updatePinterestStats(SocialAccount $account): bool
    {
        try {
            $res = Http::withToken($account->access_token)
                ->get('https://api.pinterest.com/v5/user_account');

            if ($res->successful()) {
                $user = $res->json();
                $account->username = $user['username'] ?? $account->username;
                $account->followers_count = $user['follower_count'] ?? $account->followers_count;
                
                // Fetch weekly analytics if professional
                $analyticsRes = Http::withToken($account->access_token)
                    ->get('https://api.pinterest.com/v5/user_account/analytics', [
                        'start_date' => now()->subDays(30)->toDateString(),
                        'end_date' => now()->toDateString(),
                        'columns' => 'IMPRESSION,SAVE,CLICKTHROUGH',
                    ]);

                $history = (array)($account->analytics_data['history'] ?? []);
                if ($analyticsRes->successful()) {
                    $stats = $analyticsRes->json('all.summary') ?? [];
                    Log::info("Pinterest analytics summary for {$account->id}", $stats);
                    // Add current stats to history
                    $history[] = [
                        now()->toDateString(),
                        $stats['IMPRESSION'] ?? 0,
                        $stats['SAVE'] ?? 0,
                        $stats['CLICKTHROUGH'] ?? 0,
                    ];
                }

                $account->analytics_data = array_merge((array)$account->analytics_data, [
                    'profile_image' => $user['profile_image'] ?? null,
                    'website_url' => $user['website_url'] ?? null,
                    'last_synced_at' => now()->toIso8601String(),
                    'history' => array_slice($history, -30), // keep last 30 days
                ]);

                $account->save();
                Log::info("Pinterest stats successfully updated for account {$account->id}");
                return true;
            }
            
            Log::warning("Pinterest stats fetch failed for ID {$account->id}", [
                'status' => $res->status(),
                'body' => $res->body()
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error("Pinterest stats fetch ERROR for ID {$account->id}: " . $e->getMessage());
            return false;
        }
    }
}
