<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Services\YouTubeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;

class VideoController extends Controller
{
    public function index(): JsonResponse
    {
        $videos = Video::orderBy('sort_order')->get()->map(fn ($v) => [
            'id' => $v->id,
            'videoId' => $v->video_id,
            'title' => $v->title,
            'desc' => $v->desc,
            'embedUrl' => $v->embed_url,
            'watchUrl' => $v->watch_url,
        ]);

        return response()->json(['videos' => $videos]);
    }

    /** Latest YouTube Shorts for Brand page (max 3). Prefers @StarJDs from API when key set, else DB, else default. ?refresh=1 bypasses cache. */
    public function shorts(): JsonResponse
    {
        $youtube = app(YouTubeService::class);
        $refresh = request()->boolean('refresh');
        $apiKey = config('services.youtube.api_key') ?: env('YOUTUBE_API_KEY');
        $apiShorts = $apiKey ? $youtube->getLatestShorts(3, $refresh) : [];

        if (! empty($apiShorts)) {
            $list = collect($apiShorts);
            while ($list->count() < 3) {
                $defaults = self::defaultShorts();
                $list->push($defaults[$list->count()] ?? $defaults[0]);
            }
            return response()->json(['shorts' => $list->take(3)]);
        }

        $shorts = collect();
        if (Schema::hasColumn((new Video)->getTable(), 'is_short')) {
            $shorts = Video::where('is_short', true)->orderBy('sort_order')->limit(3)->get();
        }
        if ($shorts->isEmpty()) {
            $shorts = Video::orderBy('sort_order')->limit(3)->get();
        }
        if ($shorts->isNotEmpty()) {
            $list = $shorts->map(fn ($v) => [
                'videoId' => $v->video_id,
                'embedUrl' => $v->embed_url,
                'watchUrl' => $v->watch_url,
                'poster' => 'https://img.youtube.com/vi/' . $v->video_id . '/sddefault.jpg',
                'title' => $v->title ?? 'Short',
            ]);
            return response()->json(['shorts' => $list]);
        }

        return response()->json(['shorts' => collect(self::defaultShorts())]);
    }

    /** Default 3 YouTube Shorts when DB has none. Set .env YOUTUBE_SHORTS_IDS=id1,id2,id3 for @StarJDs/shorts. */
    public static function defaultShorts(): array
    {
        $envIds = env('YOUTUBE_SHORTS_IDS', '');
        if ($envIds !== '') {
            $ids = array_slice(array_filter(array_map('trim', explode(',', $envIds))), 0, 3);
            if (! empty($ids)) {
                $defaults = ['wrYFrnrWBmw', '0lbMSg7tWxk', 'EAsrw-DfZN4'];
                while (count($ids) < 3) {
                    $ids[] = $defaults[count($ids)];
                }
                return array_map(fn ($id) => [
                    'videoId' => $id,
                    'embedUrl' => 'https://www.youtube.com/embed/' . $id,
                    'watchUrl' => 'https://www.youtube.com/watch?v=' . $id,
                    'poster' => 'https://img.youtube.com/vi/' . $id . '/sddefault.jpg',
                    'title' => 'Short',
                ], $ids);
            }
        }
        $ids = [
            ['id' => 'wrYFrnrWBmw', 'title' => 'Short 1'],
            ['id' => '0lbMSg7tWxk', 'title' => 'Short 2'],
            ['id' => 'EAsrw-DfZN4', 'title' => 'Short 3'],
        ];
        return array_map(fn ($item) => [
            'videoId' => $item['id'],
            'embedUrl' => 'https://www.youtube.com/embed/' . $item['id'],
            'watchUrl' => 'https://www.youtube.com/watch?v=' . $item['id'],
            'poster' => 'https://img.youtube.com/vi/' . $item['id'] . '/sddefault.jpg',
            'title' => $item['title'],
        ], $ids);
    }
}
