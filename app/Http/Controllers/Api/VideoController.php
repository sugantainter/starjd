<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\JsonResponse;

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
}
