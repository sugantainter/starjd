<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{
    public const LOGO_MAX_KB = 1024; // 1MB

    public function index(): JsonResponse
    {
        $items = Partner::orderBy('sort_order')->get();
        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
        ]);
        $data['sort_order'] = $data['sort_order'] ?? Partner::max('sort_order') + 1;
        if ($request->hasFile('logo')) {
            $request->validate(['logo' => ['image', 'mimes:jpeg,png,jpg,webp,svg', 'max:' . self::LOGO_MAX_KB]]);
            $data['logo'] = $request->file('logo')->store('partners', 'public');
        }
        $partner = Partner::create($data);
        return response()->json(['message' => 'Created', 'partner' => $partner->fresh()]);
    }

    public function update(Request $request, Partner $partner): JsonResponse
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
        ]);
        if ($request->hasFile('logo')) {
            $request->validate(['logo' => ['image', 'mimes:jpeg,png,jpg,webp,svg', 'max:' . self::LOGO_MAX_KB]]);
            if ($partner->logo) {
                Storage::disk('public')->delete($partner->logo);
            }
            $data['logo'] = $request->file('logo')->store('partners', 'public');
        }
        $partner->update($data);
        return response()->json(['message' => 'Updated', 'partner' => $partner->fresh()]);
    }

    public function destroy(Partner $partner): JsonResponse
    {
        if ($partner->logo) {
            Storage::disk('public')->delete($partner->logo);
        }
        $partner->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:' . self::LOGO_MAX_KB],
        ]);
        $path = $request->file('logo')->store('partners', 'public');
        $url = asset('storage/' . $path);
        return response()->json(['url' => $url, 'path' => $path]);
    }
}
