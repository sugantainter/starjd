<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FaqController extends Controller
{
    public function index(): JsonResponse
    {
        $items = DB::table('faqs')->orderBy('sort_order')->get();
        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['created_at'] = $data['updated_at'] = now();
        DB::table('faqs')->insert($data);
        return response()->json(['message' => 'Created', 'id' => DB::getPdo()->lastInsertId()]);
    }

    public function update(Request $request, int $faq): JsonResponse
    {
        $data = $request->validate([
            'question' => 'sometimes|string|max:500',
            'answer' => 'sometimes|string',
            'sort_order' => 'nullable|integer',
        ]);
        $data['updated_at'] = now();
        DB::table('faqs')->where('id', $faq)->update($data);
        return response()->json(['message' => 'Updated']);
    }

    public function destroy(int $faq): JsonResponse
    {
        DB::table('faqs')->where('id', $faq)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
