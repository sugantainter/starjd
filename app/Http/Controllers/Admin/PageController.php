<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Page::with(['state:id,name,slug', 'city:id,name,slug,state_id'])->orderBy('sort_order')->orderBy('title');
        if ($request->filled('scope')) {
            if ($request->scope === 'global') {
                $query->global();
            } elseif ($request->scope === 'state' && $request->filled('state_id')) {
                $query->where('state_id', $request->state_id)->whereNull('city_id');
            } elseif ($request->scope === 'city' && $request->filled('city_id')) {
                $query->where('city_id', $request->city_id);
            }
        }
        $pages = $query->get();
        return response()->json($pages);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'template' => 'nullable|string|max:50',
            'status' => 'nullable|in:draft,published',
            'sort_order' => 'nullable|integer',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['status'] = $data['status'] ?? 'draft';
        $data['sort_order'] = $data['sort_order'] ?? 0;
        if (!empty($data['city_id'])) {
            $data['state_id'] = \App\Models\City::find($data['city_id'])->state_id ?? null;
        } elseif (empty($data['state_id'])) {
            $data['state_id'] = null;
            $data['city_id'] = null;
        } else {
            $data['city_id'] = null;
        }
        $page = Page::create($data);
        $page->load(['state:id,name,slug', 'city:id,name,slug,state_id']);
        return response()->json(['message' => 'Created', 'page' => $page]);
    }

    public function update(Request $request, Page $page): JsonResponse
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'template' => 'nullable|string|max:50',
            'status' => 'nullable|in:draft,published',
            'sort_order' => 'nullable|integer',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
        ]);
        if (isset($data['city_id']) && $data['city_id']) {
            $data['state_id'] = \App\Models\City::find($data['city_id'])->state_id ?? $page->state_id;
        } elseif (isset($data['state_id']) && !$data['state_id']) {
            $data['city_id'] = null;
        } elseif (! array_key_exists('city_id', $data) && ! array_key_exists('state_id', $data)) {
            // leave as is
        } elseif (isset($data['state_id']) && $data['state_id']) {
            $data['city_id'] = null;
        }
        $page->update($data);
        $page->load(['state:id,name,slug', 'city:id,name,slug,state_id']);
        return response()->json(['message' => 'Updated', 'page' => $page->fresh()]);
    }

    public function destroy(Page $page): JsonResponse
    {
        $page->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
