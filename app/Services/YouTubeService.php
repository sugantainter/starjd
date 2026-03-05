<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YouTubeService
{
    private const BASE_URL = 'https://www.googleapis.com/youtube/v3';

    /** Max duration in seconds for a video to be considered a Short (YT can report 61s for 60s shorts). */
    private const SHORTS_MAX_DURATION = 61;

    /** Cache TTL for latest Shorts (seconds). */
    private const CACHE_TTL = 3600;

    public function __construct(
        private ?string $apiKey = null,
        private string $channelHandle = 'StarJDs'
    ) {
        $this->apiKey = $apiKey ?? config('services.youtube.api_key');
        $this->channelHandle = ltrim(config('services.youtube.channel_handle', $channelHandle), '@');
    }

    /**
     * Get up to 3 latest Shorts from the configured channel (videos ≤61s from uploads).
     * Returns array of [videoId, embedUrl, watchUrl, poster, title]. Empty on failure.
     * Set $refresh = true to bypass cache (e.g. ?refresh=1 on /api/shorts).
     */
    public function getLatestShorts(int $limit = 3, bool $refresh = false): array
    {
        if (empty($this->apiKey)) {
            Log::debug('YouTubeService: YOUTUBE_API_KEY not set, skipping API.');
            return [];
        }

        $cacheKey = 'youtube_latest_shorts_' . $this->channelHandle;
        if ($refresh) {
            Cache::forget($cacheKey);
        }

        $result = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($limit) {
            $uploadsPlaylistId = $this->getUploadsPlaylistId();
            if ($uploadsPlaylistId === null) {
                return [];
            }

            $videoIds = $this->getRecentUploadIds($uploadsPlaylistId, 100);
            if (empty($videoIds)) {
                return [];
            }

            $shorts = $this->fetchShortsFromVideoIds($videoIds, $limit);
            return array_map(fn ($v) => [
                'videoId' => $v['id'],
                'embedUrl' => 'https://www.youtube.com/embed/' . $v['id'],
                'watchUrl' => 'https://www.youtube.com/watch?v=' . $v['id'],
                'poster' => 'https://img.youtube.com/vi/' . $v['id'] . '/sddefault.jpg',
                'title' => $v['title'] ?? 'Short',
            ], $shorts);
        });

        if (empty($result)) {
            Cache::forget($cacheKey);
        }

        return $result;
    }

    private function getUploadsPlaylistId(): ?string
    {
        $res = Http::get(self::BASE_URL . '/channels', [
            'part' => 'contentDetails',
            'forHandle' => $this->channelHandle,
            'key' => $this->apiKey,
        ]);

        if (! $res->successful()) {
            Log::warning('YouTube API channels.list failed', ['body' => $res->body(), 'status' => $res->status()]);
            return null;
        }

        $items = $res->json('items');
        if (empty($items)) {
            return null;
        }

        return $items[0]['contentDetails']['relatedPlaylists']['uploads'] ?? null;
    }

    /** @return string[] */
    private function getRecentUploadIds(string $playlistId, int $maxResults): array
    {
        $res = Http::get(self::BASE_URL . '/playlistItems', [
            'part' => 'contentDetails',
            'playlistId' => $playlistId,
            'maxResults' => $maxResults,
            'key' => $this->apiKey,
        ]);

        if (! $res->successful()) {
            return [];
        }

        $items = $res->json('items', []);
        $ids = [];
        foreach ($items as $item) {
            $vid = $item['contentDetails']['videoId'] ?? null;
            if ($vid) {
                $ids[] = $vid;
            }
        }

        return $ids;
    }

    /**
     * Fetch video details and return only those with duration ≤ SHORTS_MAX_DURATION (up to $limit).
     * @param string[] $videoIds
     * @return array<int, array{id: string, title: string}>
     */
    private function fetchShortsFromVideoIds(array $videoIds, int $limit): array
    {
        $chunks = array_chunk($videoIds, 50);
        $shorts = [];

        foreach ($chunks as $chunk) {
            $res = Http::get(self::BASE_URL . '/videos', [
                'part' => 'contentDetails,snippet',
                'id' => implode(',', $chunk),
                'key' => $this->apiKey,
            ]);

            if (! $res->successful()) {
                continue;
            }

            foreach ($res->json('items', []) as $video) {
                $duration = $video['contentDetails']['duration'] ?? '';
                if ($this->durationToSeconds($duration) <= self::SHORTS_MAX_DURATION) {
                    $shorts[] = [
                        'id' => $video['id'],
                        'title' => $video['snippet']['title'] ?? 'Short',
                    ];
                    if (count($shorts) >= $limit) {
                        return $shorts;
                    }
                }
            }
        }

        return $shorts;
    }

    /** Parse ISO 8601 duration (e.g. PT1M30S, PT45S) to seconds. */
    private function durationToSeconds(string $duration): int
    {
        if ($duration === '') {
            return PHP_INT_MAX;
        }

        if (preg_match('/^PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?$/i', $duration, $m)) {
            $h = (int) ($m[1] ?? 0);
            $min = (int) ($m[2] ?? 0);
            $s = (int) ($m[3] ?? 0);
            return $h * 3600 + $min * 60 + $s;
        }

        return PHP_INT_MAX;
    }
}
