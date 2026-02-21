<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageCategory;
use App\Models\PackageItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreatorPackageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $packages = $request->user()->packages()
            ->with(['packageCategory', 'items'])
            ->orderBy('sort_order')
            ->get();
        return response()->json($packages);
    }

    public function categories(): JsonResponse
    {
        $categories = PackageCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug']);
        return response()->json($categories);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'package_category_id' => ['nullable', 'integer', 'exists:package_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'deliverables' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'items' => ['nullable', 'array'],
            'items.*.name' => ['required_with:items', 'string', 'max:255'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.unit_price' => ['required_with:items', 'numeric', 'min:0'],
        ]);

        $max = $request->user()->packages()->max('sort_order') ?? 0;
        $package = $request->user()->packages()->create([
            'package_category_id' => $request->input('package_category_id'),
            'name' => $request->name,
            'price' => (float) $request->price,
            'description' => $request->description,
            'deliverables' => $request->deliverables,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $max + 1,
        ]);

        $this->syncItems($package, $request->input('items', []));
        $package->load(['packageCategory', 'items']);
        return response()->json($package, 201);
    }

    public function update(Request $request, Package $package): JsonResponse
    {
        if ($package->creator_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $request->validate([
            'package_category_id' => ['nullable', 'integer', 'exists:package_categories,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'deliverables' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'items' => ['nullable', 'array'],
            'items.*.id' => ['sometimes', 'integer', 'exists:package_items,id'],
            'items.*.name' => ['required_with:items', 'string', 'max:255'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.unit_price' => ['required_with:items', 'numeric', 'min:0'],
        ]);

        $package->update($request->only([
            'package_category_id', 'name', 'price', 'description', 'deliverables', 'is_active', 'sort_order',
        ]));

        $this->syncItems($package, $request->input('items', []));
        $package->load(['packageCategory', 'items']);
        return response()->json($package);
    }

    public function destroy(Request $request, Package $package): JsonResponse
    {
        if ($package->creator_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $package->delete();
        return response()->json(null, 204);
    }

    private function syncItems(Package $package, array $items): void
    {
        $ids = [];
        foreach ($items as $i => $row) {
            $data = [
                'name' => $row['name'],
                'quantity' => (int) ($row['quantity'] ?? 1),
                'unit_price' => (float) ($row['unit_price'] ?? 0),
                'sort_order' => $i,
            ];
            if (! empty($row['id'])) {
                $item = $package->items()->where('id', $row['id'])->first();
                if ($item) {
                    $item->update($data);
                    $ids[] = $item->id;
                    continue;
                }
            }
            $item = $package->items()->create($data);
            $ids[] = $item->id;
        }
        $package->items()->whereNotIn('id', $ids)->delete();
    }
}
