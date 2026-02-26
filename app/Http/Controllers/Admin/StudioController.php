<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Studio;
use App\Models\StudioCategory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudioController extends Controller
{
    /**
     * GET /api/admin/studio-owners — List users with studio_owner role (for assign when adding studio).
     */
    public function studioOwners(): JsonResponse
    {
        $users = User::whereHas('roles', fn ($q) => $q->where('slug', 'studio_owner'))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }

    /**
     * POST /api/admin/studios — Admin create a studio (assign to a studio owner).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
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
            'status' => ['nullable', 'string', 'in:draft,active,inactive,suspended'],
            'amenity_ids' => ['nullable', 'array'],
            'amenity_ids.*' => ['integer', 'exists:amenities,id'],
        ]);

        $slug = Str::slug($request->name) . '-' . substr(uniqid(), -4);
        while (Studio::where('slug', $slug)->exists()) {
            $slug = Str::slug($request->name) . '-' . substr(uniqid(), -4);
        }

        $category = StudioCategory::find($request->category_id);
        $studio = Studio::create([
            'user_id' => $request->user_id,
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
            'status' => $request->input('status', 'draft'),
        ]);

        if ($request->filled('amenity_ids') && is_array($request->amenity_ids)) {
            $studio->amenities()->sync(array_map('intval', $request->amenity_ids));
        }
        $studio->load(['category:id,name,slug', 'amenities:id,name,slug', 'user:id,name,email']);

        return response()->json($studio, 201);
    }

    /**
     * GET /api/admin/studios — List all studios (for approval/management).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Studio::with(['user:id,name,email', 'category:id,name,slug'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = min(max((int) $request->input('per_page', 15), 1), 50);
        $studios = $query->paginate($perPage);

        $studios->getCollection()->transform(function (Studio $s) {
            $cat = $s->relationLoaded('category') ? $s->getRelation('category') : $s->category()->first();
            return [
                'id' => $s->id,
                'name' => $s->name,
                'slug' => $s->slug,
                'status' => $s->status,
                'is_featured' => (bool) $s->is_featured,
                'city' => $s->city,
                'price_per_hour' => $s->price_per_hour ? (float) $s->price_per_hour : null,
                'price_per_day' => $s->price_per_day ? (float) $s->price_per_day : null,
                'category' => $cat ? ['id' => $cat->id, 'name' => $cat->name] : null,
                'owner' => $s->user ? ['id' => $s->user->id, 'name' => $s->user->name, 'email' => $s->user->email] : null,
                'created_at' => $s->created_at->toIso8601String(),
            ];
        });

        return response()->json($studios);
    }

    /**
     * GET /api/admin/studios/{studio} — Single studio for admin edit/view.
     */
    public function show(Studio $studio): JsonResponse
    {
        $studio->load(['category:id,name,slug', 'amenities:id,name,slug', 'images', 'user:id,name,email']);

        $cat = $studio->relationLoaded('category') ? $studio->getRelation('category') : $studio->category()->first();
        $data = $studio->only([
            'id', 'slug', 'name', 'description', 'address', 'city', 'state', 'pincode',
            'latitude', 'longitude', 'price_per_hour', 'price_per_day', 'cancellation_policy', 'status', 'is_featured',
        ]);
        $data['category_id'] = $studio->category_id;
        $data['category'] = $cat ? ['id' => $cat->id, 'name' => $cat->name, 'slug' => $cat->slug] : null;
        $data['user_id'] = $studio->user_id;
        $data['owner'] = $studio->user ? ['id' => $studio->user->id, 'name' => $studio->user->name, 'email' => $studio->user->email] : null;
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
     * PUT /api/admin/studios/{studio} — Update studio (full or partial: status, featured, or all fields).
     */
    public function update(Request $request, Studio $studio): JsonResponse
    {
        $request->validate([
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
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
            'is_featured' => ['nullable', 'boolean'],
            'amenity_ids' => ['nullable', 'array'],
            'amenity_ids.*' => ['integer', 'exists:amenities,id'],
        ]);

        $fill = [
            'name', 'category_id', 'description', 'address', 'city', 'state', 'pincode',
            'latitude', 'longitude', 'price_per_hour', 'price_per_day', 'cancellation_policy', 'status', 'is_featured',
        ];
        if ($request->has('user_id')) {
            $studio->user_id = $request->user_id;
        }
        if (array_key_exists('is_featured', $request->all())) {
            $studio->is_featured = (bool) $request->is_featured;
        }
        if ($request->has('category_id')) {
            $category = StudioCategory::find($request->category_id);
            $studio->setAttribute('category', $category ? $category->slug : $studio->getAttribute('category'));
        }
        $studio->update($request->only($fill));

        if (array_key_exists('amenity_ids', $request->all())) {
            $studio->amenities()->sync($request->amenity_ids ? array_map('intval', $request->amenity_ids) : []);
        }

        return response()->json([
            'message' => 'Updated.',
            'studio' => ['id' => $studio->id, 'name' => $studio->name, 'status' => $studio->status, 'is_featured' => (bool) $studio->is_featured],
        ]);
    }
}
