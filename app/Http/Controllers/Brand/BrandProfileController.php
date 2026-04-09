<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandProfileController extends Controller
{
    public const LOGO_MAX_SIZE_KB = 2048;

    public function show(Request $request): JsonResponse
    {
        $profile = $request->user()->brandProfile;
        if (! $profile) {
            $profile = $request->user()->brandProfile()->create([]);
        }
        $profile->load(['user' => function ($q) {
            $q->select('id', 'name', 'state_id', 'city_id')->with(['state:id,name', 'city:id,name']);
        }]);
        return response()->json($profile);
    }

    public function update(Request $request): JsonResponse
    {
        // Normalize website so common inputs like "example.com" become valid URLs.
        if ($request->filled('website')) {
            $website = trim($request->input('website'));
            if (! str_starts_with($website, 'http://') && ! str_starts_with($website, 'https://')) {
                $website = 'https://' . $website;
            }
            $request->merge(['website' => $website]);
        }

        $rules = [
            'company_name' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'url', 'max:255'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'industry' => ['nullable', 'string', 'max:255'],
            'hq_location' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:brand_profiles,slug,' . ($request->user()->brandProfile?->id ?? 'NULL')],
            'is_public' => ['nullable', 'boolean'],
            'state_id' => ['nullable', 'exists:states,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
        ];

        if ($request->hasFile('logo')) {
            $rules['logo'] = ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:' . self::LOGO_MAX_SIZE_KB];
        }

        $request->validate($rules);

        $user = $request->user();
        $profile = $user->brandProfile;
        if (! $profile) {
            $profile = $user->brandProfile()->create([]);
        }

        $data = $request->only(['company_name', 'website', 'bio', 'industry', 'hq_location', 'slug', 'is_public']);

        if ($request->hasFile('logo')) {
            if ($profile->logo) {
                Storage::delete($profile->logo);
            }
            $path = $request->file('logo')->store(
                'profiles/logos/' . $user->id
            );
            $data['logo'] = $path;
        }

        $profile->update($data);

        $request->user()->update($request->only(['state_id', 'city_id']));

        return response()->json([
            'message' => 'Brand profile updated successfully.',
            'profile' => $profile->fresh()->load(['user' => function ($q) {
                $q->select('id', 'name', 'state_id', 'city_id')->with(['state:id,name', 'city:id,name']);
            }]),
        ]);
    }
}
