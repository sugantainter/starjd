<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
        return response()->json($profile);
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

        $request->validate($rules);

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
            $path = $request->file('avatar')->store(
                'profiles/avatars/' . $request->user()->id,
                'public'
            );
            $data['avatar'] = $path;
        }

        $profile->update($data);

        if ($request->has('slug') && $request->input('slug') !== $profile->slug) {
            $request->validate(['slug' => ['required', 'string', 'max:100', 'unique:creator_profiles,slug,' . $profile->id]]);
            $profile->update(['slug' => Str::slug($request->input('slug')) ?: $profile->slug]);
        }

        return response()->json($profile->fresh());
    }
}
