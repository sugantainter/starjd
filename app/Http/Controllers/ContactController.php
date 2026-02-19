<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        try {
            DB::table('contact_messages')->insert([
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'body' => $request->body,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Failed to send message. Please try again.'], 500);
        }

        return response()->json(['message' => 'Thank you! We will get back to you soon.']);
    }
}
