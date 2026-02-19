<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Video::orderBy('sort_order')->get();
        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'video_id' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'embed_url' => 'required|string|max:500',
            'watch_url' => 'required|string|max:500',
            'sort_order' => 'nullable|integer',
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $video = Video::create($data);
        return response()->json(['message' => 'Created', 'video' => $video]);
    }

    public function update(Request $request, int $video): JsonResponse
    {
        $model = Video::findOrFail($video);
        $data = $request->validate([
            'video_id' => 'sometimes|string|max:50',
            'title' => 'sometimes|string|max:255',
            'desc' => 'nullable|string',
            'embed_url' => 'sometimes|string|max:500',
            'watch_url' => 'sometimes|string|max:500',
            'sort_order' => 'nullable|integer',
        ]);
        $model->update($data);
        return response()->json(['message' => 'Updated', 'video' => $model->fresh()]);
    }

    public function destroy(int $video): JsonResponse
    {
        Video::findOrFail($video)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
