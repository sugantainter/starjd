<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestimonialController extends Controller
{
    public function index(): JsonResponse
    {
        $items = DB::table('testimonials')->orderBy('sort_order')->get();
        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'quote' => 'required|string',
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'avatar' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['created_at'] = $data['updated_at'] = now();
        DB::table('testimonials')->insert($data);
        return response()->json(['message' => 'Created', 'id' => DB::getPdo()->lastInsertId()]);
    }

    public function update(Request $request, int $testimonial): JsonResponse
    {
        $data = $request->validate([
            'quote' => 'sometimes|string',
            'name' => 'sometimes|string|max:255',
            'role' => 'nullable|string|max:255',
            'avatar' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
        ]);
        $data['updated_at'] = now();
        DB::table('testimonials')->where('id', $testimonial)->update($data);
        return response()->json(['message' => 'Updated']);
    }

    public function destroy(int $testimonial): JsonResponse
    {
        DB::table('testimonials')->where('id', $testimonial)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
