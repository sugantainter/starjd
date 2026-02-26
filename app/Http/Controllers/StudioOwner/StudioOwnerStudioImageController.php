<?php

namespace App\Http\Controllers\StudioOwner;

use App\Http\Controllers\Controller;
use App\Models\Studio;
use App\Models\StudioImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudioOwnerStudioImageController extends Controller
{
    private const IMAGE_MAX_KB = 5120;

    public function store(Request $request, Studio $studio): JsonResponse
    {
        $this->authorize('update', $studio);

        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:' . self::IMAGE_MAX_KB],
            'caption' => ['nullable', 'string', 'max:255'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        $path = $request->file('image')->store('studios/' . $studio->id, 'public');
        if (! $path) {
            return response()->json(['message' => 'Upload failed.'], 500);
        }

        $maxOrder = (int) $studio->images()->max('sort_order');
        if ($request->boolean('is_primary')) {
            $studio->images()->update(['is_primary' => false]);
        }

        $image = $studio->images()->create([
            'image' => $path,
            'caption' => $request->input('caption'),
            'sort_order' => $maxOrder + 1,
            'is_primary' => $request->boolean('is_primary'),
        ]);

        return response()->json([
            'message' => 'Image added.',
            'image' => [
                'id' => $image->id,
                'image' => '/storage/' . ltrim($image->image, '/'),
                'caption' => $image->caption,
                'sort_order' => $image->sort_order,
                'is_primary' => $image->is_primary,
            ],
        ], 201);
    }

    public function destroy(StudioImage $studio_image): JsonResponse
    {
        $this->authorize('update', $studio_image->studio);

        if ($studio_image->image && ! str_starts_with($studio_image->image, 'http')) {
            Storage::disk('public')->delete($studio_image->image);
        }
        $studio_image->delete();

        $studio = $studio_image->studio;
        if ($studio->images()->where('is_primary', true)->doesntExist()) {
            $studio->images()->orderBy('sort_order')->first()?->update(['is_primary' => true]);
        }

        return response()->json(['message' => 'Image removed.']);
    }

    public function reorder(Request $request, Studio $studio): JsonResponse
    {
        $this->authorize('update', $studio);

        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:studio_images,id'],
            'primary_id' => ['nullable', 'integer', 'exists:studio_images,id'],
        ]);

        foreach ($request->input('order', []) as $index => $id) {
            StudioImage::where('studio_id', $studio->id)->where('id', $id)->update(['sort_order' => $index]);
        }
        if ($request->filled('primary_id')) {
            $studio->images()->update(['is_primary' => false]);
            $studio->images()->where('id', $request->primary_id)->update(['is_primary' => true]);
        }

        $studio->load('images');
        return response()->json([
            'message' => 'Updated.',
            'images' => $studio->images->map(fn ($img) => [
                'id' => $img->id,
                'image' => '/storage/' . ltrim($img->image, '/'),
                'caption' => $img->caption,
                'sort_order' => $img->sort_order,
                'is_primary' => $img->is_primary,
            ]),
        ]);
    }
}
