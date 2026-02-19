<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroController extends Controller
{
    public const HERO_IMAGE_MAX_KB = 5120; // 5MB

    public function show(): JsonResponse
    {
        $row = HeroSetting::first();
        $hero = $row ? $row->toArray() : HeroSetting::getForPublic();
        return response()->json($hero);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'headline' => 'nullable|string|max:255',
            'subheadline' => 'nullable|string|max:255',
            'wallpaper_images' => 'nullable|array',
            'wallpaper_images.*.src' => 'required|string|max:2000',
            'cascade_images' => 'nullable|array',
            'cascade_images.*.src' => 'required|string|max:2000',
            'cascade_images.*.alt' => 'nullable|string|max:255',
            'cascade_images.*.link' => 'nullable|string|max:500',
            'btn_creator_label' => 'nullable|string|max:100',
            'btn_creator_url' => 'nullable|string|max:500',
            'btn_brand_label' => 'nullable|string|max:100',
            'btn_brand_url' => 'nullable|string|max:500',
            'btn_browse_label' => 'nullable|string|max:100',
            'btn_browse_url' => 'nullable|string|max:500',
        ]);

        $row = HeroSetting::first();
        if (! $row) {
            $row = new HeroSetting();
        }
        $row->fill($data);
        $row->save();

        return response()->json(['message' => 'Hero updated', 'hero' => HeroSetting::getForPublic()]);
    }

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:' . self::HERO_IMAGE_MAX_KB],
        ]);

        $file = $request->file('image');
        $path = $file->store('hero/' . date('Y-m-d'), 'public');

        $url = asset('storage/' . $path);

        return response()->json(['url' => $url, 'path' => $path]);
    }
}
