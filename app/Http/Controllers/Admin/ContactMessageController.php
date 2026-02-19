<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ContactMessageController extends Controller
{
    public function index(): JsonResponse
    {
        $items = DB::table('contact_messages')->orderByDesc('created_at')->get();
        return response()->json($items);
    }

    public function destroy(int $id): JsonResponse
    {
        DB::table('contact_messages')->where('id', $id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
