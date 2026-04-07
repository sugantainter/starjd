<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Support\StoragePublicUrl;
use App\Models\CreatorProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CreatorProfileController extends Controller
{
    public const AVATAR_MAX_SIZE_KB = 2048;

    public function show(Request $request): JsonResponse
    {
        $profile = $request->user()->creatorProfile;
        if (! $profile) {
            $profile = $request->user()->creatorProfile()->create([]);
        }
        $this->ensureCreatorProfileHasSlug($request, $profile);
        $profile->load(['user' => function ($q) {
            $q->select('id', 'name', 'state_id', 'city_id')->with(['state:id,name', 'city:id,name']);
        }]);
        return response()->json($this->profileWithAvatarUrl($profile));
    }

    public function update(Request $request): JsonResponse
    {
        // Get categories from database or fallback to config
        $categories = [];
        if (Schema::hasTable('categories')) {
            $categories = DB::table('categories')->pluck('name')->toArray();
        }
        if (empty($categories)) {
            $categories = config('creator.categories', []);
        }
        
        $rules = [
            'bio' => ['nullable', 'string', 'max:2000'],
            'location' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', Rule::in($categories)],
            'gender' => ['nullable', 'string', Rule::in(array_keys(config('creator.genders', [])))],
            'language' => ['nullable', 'string', Rule::in(config('creator.languages', []))],
            'is_public' => ['nullable', 'boolean'],
            'min_rate' => ['nullable', 'numeric', 'min:0'],
            'engagement_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'state_id' => ['nullable', 'exists:states,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
        ];

        if ($request->hasFile('avatar')) {
            $rules['avatar'] = ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:' . self::AVATAR_MAX_SIZE_KB];
        }

        $messages = [
            'avatar.required' => 'Please select a profile photo to upload.',
            'avatar.image' => 'The file must be an image (JPEG, PNG, JPG, or WebP).',
            'avatar.mimes' => 'The profile photo must be a JPEG, PNG, JPG, or WebP file.',
            'avatar.max' => 'The profile photo must not be larger than 2 MB.',
        ];

        $request->validate($rules, $messages);

        $profile = $request->user()->creatorProfile;
        if (! $profile) {
            $profile = $request->user()->creatorProfile()->create([]);
        }

        $data = $request->only(['bio', 'location', 'tagline', 'category', 'gender', 'language', 'min_rate', 'engagement_rate']);
        $data['is_public'] = $request->boolean('is_public');

        if ($request->hasFile('avatar')) {
            if ($profile->avatar) {
                Storage::delete($profile->avatar);
            }
            $dir = 'profiles/avatars/' . $request->user()->id;
            $path = $request->file('avatar')->store($dir);
            if (! $path) {
                throw new HttpException(500, 'Profile photo could not be saved to cloud storage.');
            }
            $data['avatar'] = $path;
        }

        $profile->update($data);

        $request->user()->update($request->only(['state_id', 'city_id']));

        // Only treat non-empty slug as an intentional change (empty string = leave current slug).
        if ($request->filled('slug') && $request->input('slug') !== $profile->slug) {
            $request->validate(['slug' => ['required', 'string', 'max:100', 'unique:creator_profiles,slug,' . $profile->id]]);
            $profile->update(['slug' => Str::slug($request->input('slug')) ?: $profile->slug]);
        }

        $updated = $profile->fresh();
        $this->ensureCreatorProfileHasSlug($request, $updated);
        $updated->load(['user' => function ($q) {
            $q->select('id', 'name', 'state_id', 'city_id')->with(['state:id,name', 'city:id,name']);
        }]);
        return response()->json($this->profileWithAvatarUrl($updated));
    }

    /**
     * Backfill slug for legacy rows or edge cases where slug is null/empty.
     */
    private function ensureCreatorProfileHasSlug(Request $request, CreatorProfile $profile): void
    {
        if (! blank($profile->slug)) {
            return;
        }
        $profile->slug = CreatorProfile::generateUniqueSlugForUser($request->user(), $profile->id);
        $profile->save();
    }

    /**
     * Build profile with avatar_url. Use current request host so the image URL always matches the site (like image posts).
     */
    private function profileWithAvatarUrl($profile): array
    {
        $data = $profile->toArray();
        if (! empty($profile->avatar)) {
            $url = StoragePublicUrl::resolve($profile->avatar);
            $ts = $profile->updated_at?->timestamp ?? time();
            $data['avatar_url'] = $url !== null
                ? $url.(str_contains($url, '?') ? '&' : '?').'t='.$ts
                : null;
        } else {
            $data['avatar_url'] = null;
        }
        return $data;
    }
}
