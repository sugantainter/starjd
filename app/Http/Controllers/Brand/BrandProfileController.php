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
        return response()->json($profile);
    }

    public function update(Request $request): JsonResponse
    {
        $rules = [
            'company_name' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'url', 'max:255'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ];

        if ($request->hasFile('logo')) {
            $rules['logo'] = ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:' . self::LOGO_MAX_SIZE_KB];
        }

        $request->validate($rules);

        $profile = $request->user()->brandProfile;
        if (! $profile) {
            $profile = $request->user()->brandProfile()->create([]);
        }

        $data = $request->only(['company_name', 'website', 'bio']);

        if ($request->hasFile('logo')) {
            if ($profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            $path = $request->file('logo')->store(
                'profiles/logos/' . $request->user()->id,
                'public'
            );
            $data['logo'] = $path;
        }

        $profile->update($data);
        return response()->json($profile->fresh());
    }
}
