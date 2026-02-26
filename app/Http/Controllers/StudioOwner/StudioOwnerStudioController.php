<?php

namespace App\Http\Controllers\StudioOwner;

use App\Http\Controllers\Controller;
use App\Models\Studio;
use App\Models\StudioCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudioOwnerStudioController extends Controller
{
    /**
     * GET /api/studio-owner/studios — List studios owned by current user.
     */
    public function index(Request $request): JsonResponse
    {
        $studios = $request->user()
            ->studios()
            ->with(['category:id,name,slug', 'images'])
            ->orderByDesc('created_at')
            ->paginate((int) $request->input('per_page', 15));

        $studios->getCollection()->transform(function (Studio $s) {
            $main = $s->images()->where('is_primary', true)->first() ?? $s->images()->orderBy('sort_order')->first();
            $category = $s->relationLoaded('category') ? $s->getRelation('category') : $s->category()->first();
            return [
                'id' => $s->id,
                'slug' => $s->slug,
                'name' => $s->name,
                'status' => $s->status,
                'city' => $s->city,
                'category' => $category ? ['id' => $category->id, 'name' => $category->name, 'slug' => $category->slug] : null,
                'price_per_hour' => $s->price_per_hour ? (float) $s->price_per_hour : null,
                'price_per_day' => $s->price_per_day ? (float) $s->price_per_day : null,
                'rating_avg' => $s->rating_avg ? (float) $s->rating_avg : null,
                'reviews_count' => (int) $s->reviews_count,
                'is_featured' => (bool) $s->is_featured,
                'main_image' => $main ? '/storage/' . ltrim($main->image, '/') : null,
                'created_at' => $s->created_at->toIso8601String(),
            ];
        });

        return response()->json($studios);
    }

    /**
     * GET /api/studio-owner/studios/{studio} — Single studio for editing (with images, amenities).
     */
    public function show(Studio $studio): JsonResponse
    {
        $this->authorize('update', $studio);

        $studio->load(['category:id,name,slug', 'amenities:id,name,slug', 'images']);

        $data = $studio->only([
            'id', 'slug', 'name', 'description', 'address', 'city', 'state', 'pincode',
            'latitude', 'longitude', 'price_per_hour', 'price_per_day', 'cancellation_policy', 'status',
        ]);
        $data['category_id'] = $studio->category_id;
        $cat = $studio->relationLoaded('category') ? $studio->getRelation('category') : $studio->category()->first();
        $data['category'] = $cat ? ['id' => $cat->id, 'name' => $cat->name, 'slug' => $cat->slug] : null;
        $data['amenity_ids'] = $studio->amenities->pluck('id')->all();
        $data['images'] = $studio->images->map(fn ($img) => [
            'id' => $img->id,
            'image' => '/storage/' . ltrim($img->image, '/'),
            'caption' => $img->caption,
            'sort_order' => $img->sort_order,
            'is_primary' => $img->is_primary,
        ])->values()->all();

        return response()->json($data);
    }

    /**
     * POST /api/studio-owner/studios — Create studio.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:studio_categories,id'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'price_per_hour' => ['nullable', 'numeric', 'min:0'],
            'price_per_day' => ['nullable', 'numeric', 'min:0'],
            'cancellation_policy' => ['nullable', 'string', 'in:flexible,moderate,strict'],
            'amenity_ids' => ['nullable', 'array'],
            'amenity_ids.*' => ['integer', 'exists:amenities,id'],
        ]);

        $user = $request->user();
        $slug = Str::slug($request->name) . '-' . substr(uniqid(), -4);
        while (Studio::where('slug', $slug)->exists()) {
            $slug = Str::slug($request->name) . '-' . substr(uniqid(), -4);
        }

        $category = StudioCategory::find($request->category_id);
        $studio = Studio::create([
            'user_id' => $user->id,
            'category_id' => $request->category_id,
            'category' => $category ? $category->slug : '',
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'price_per_hour' => $request->price_per_hour,
            'price_per_day' => $request->price_per_day,
            'cancellation_policy' => $request->input('cancellation_policy', 'moderate'),
            'status' => 'draft',
        ]);

        if ($request->filled('amenity_ids') && is_array($request->amenity_ids)) {
            $studio->amenities()->sync(array_map('intval', $request->amenity_ids));
        }
        $studio->load(['category:id,name,slug', 'amenities:id,name,slug']);
        return response()->json($studio, 201);
    }

    /**
     * PUT /api/studio-owner/studios/{id} — Update studio.
     */
    public function update(Request $request, Studio $studio): JsonResponse
    {
        $this->authorize('update', $studio);

        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'category_id' => ['sometimes', 'integer', 'exists:studio_categories,id'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'price_per_hour' => ['nullable', 'numeric', 'min:0'],
            'price_per_day' => ['nullable', 'numeric', 'min:0'],
            'cancellation_policy' => ['nullable', 'string', 'in:flexible,moderate,strict'],
            'status' => ['sometimes', 'string', 'in:draft,active,inactive,suspended'],
            'amenity_ids' => ['nullable', 'array'],
            'amenity_ids.*' => ['integer', 'exists:amenities,id'],
        ]);

        $data = $request->only([
            'name', 'category_id', 'description', 'address', 'city', 'state', 'pincode',
            'latitude', 'longitude', 'price_per_hour', 'price_per_day', 'cancellation_policy', 'status',
        ]);
        if (array_key_exists('category_id', $data) && $data['category_id']) {
            $cat = StudioCategory::find($data['category_id']);
            $data['category'] = $cat ? $cat->slug : $studio->category;
        }
        $studio->update($data);

        if (array_key_exists('amenity_ids', $request->all())) {
            $studio->amenities()->sync($request->amenity_ids ? array_map('intval', $request->amenity_ids) : []);
        }
        $studio->load(['category:id,name,slug', 'amenities:id,name,slug']);
        return response()->json($studio);
    }

    /**
     * DELETE /api/studio-owner/studios/{id} — Delete studio.
     */
    public function destroy(Studio $studio): JsonResponse
    {
        $this->authorize('delete', $studio);
        $studio->delete();
        return response()->json(['message' => 'Studio deleted.']);
    }
}
