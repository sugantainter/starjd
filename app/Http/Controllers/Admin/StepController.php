<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StepController extends Controller
{
    public function index(): JsonResponse
    {
        $items = DB::table('steps')->orderBy('sort_order')->get();
        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'desc' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['created_at'] = $data['updated_at'] = now();
        DB::table('steps')->insert($data);
        return response()->json(['message' => 'Created', 'id' => DB::getPdo()->lastInsertId()]);
    }

    public function update(Request $request, int $step): JsonResponse
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'desc' => 'sometimes|string',
            'sort_order' => 'nullable|integer',
        ]);
        $data['updated_at'] = now();
        DB::table('steps')->where('id', $step)->update($data);
        return response()->json(['message' => 'Updated']);
    }

    public function destroy(int $step): JsonResponse
    {
        DB::table('steps')->where('id', $step)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
