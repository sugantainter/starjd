<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            $profile = $request->user()->creatorProfile()->create([
                'slug' => Str::slug($request->user()->name) . '-' . $request->user()->id,
            ]);
        }
        return response()->json($this->profileWithAvatarUrl($profile));
    }

    public function update(Request $request): JsonResponse
    {
        $rules = [
            'bio' => ['nullable', 'string', 'max:2000'],
            'location' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', Rule::in(config('creator.categories', []))],
            'gender' => ['nullable', 'string', Rule::in(array_keys(config('creator.genders', [])))],
            'language' => ['nullable', 'string', Rule::in(config('creator.languages', []))],
            'is_public' => ['nullable', 'boolean'],
            'min_rate' => ['nullable', 'numeric', 'min:0'],
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
            $profile = $request->user()->creatorProfile()->create([
                'slug' => Str::slug($request->user()->name) . '-' . $request->user()->id,
            ]);
        }

        $data = $request->only(['bio', 'location', 'tagline', 'category', 'gender', 'language', 'min_rate']);
        $data['is_public'] = $request->boolean('is_public');

        if ($request->hasFile('avatar')) {
            if ($profile->avatar) {
                Storage::disk('public')->delete($profile->avatar);
            }
            // Same storage pattern as CreatorImagePostController (image posts) so avatar works the same way
            $path = $request->file('avatar')->store(
                'profiles/avatars/' . $request->user()->id,
                'public'
            );
            if (! $path || ! Storage::disk('public')->exists($path)) {
                throw new HttpException(500, 'Profile photo could not be saved. Check that storage/app/public is writable and run: php artisan storage:link');
            }
            $data['avatar'] = $path;
        }

        $profile->update($data);

        if ($request->has('slug') && $request->input('slug') !== $profile->slug) {
            $request->validate(['slug' => ['required', 'string', 'max:100', 'unique:creator_profiles,slug,' . $profile->id]]);
            $profile->update(['slug' => Str::slug($request->input('slug')) ?: $profile->slug]);
        }

        return response()->json($this->profileWithAvatarUrl($profile->fresh()));
    }

    /**
     * Build profile with avatar_url using same logic as CreatorImagePost (image_url) so it loads like image posts.
     */
    private function profileWithAvatarUrl($profile): array
    {
        $data = $profile->toArray();
        if (! empty($profile->avatar) && Storage::disk('public')->exists($profile->avatar)) {
            $data['avatar_url'] = Storage::disk('public')->url($profile->avatar)
                . (str_contains($profile->avatar, '?') ? '&' : '?') . 't=' . ($profile->updated_at?->timestamp ?? time());
        } else {
            $data['avatar_url'] = null;
        }
        return $data;
    }
}
