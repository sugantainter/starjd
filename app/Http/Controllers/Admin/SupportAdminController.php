<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportAdminController extends Controller
{
    public function index(): JsonResponse
    {
        $tickets = SupportTicket::with('user:id,name,email')
            ->withCount('messages')
            ->orderByDesc('updated_at')
            ->get();

        return response()->json($tickets);
    }

    public function show(SupportTicket $ticket): JsonResponse
    {
        return response()->json($ticket->load(['user:id,name,email', 'messages.user:id,name,avatar_url']));
    }

    public function reply(Request $request, SupportTicket $ticket): JsonResponse
    {
        $request->validate([
            'message' => 'required|string',
            'status' => 'nullable|in:open,in_progress,resolved,closed',
        ]);

        $message = SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'message' => $request->message,
            'is_admin' => true,
        ]);

        if ($request->has('status')) {
            $ticket->status = $request->status;
        } else {
            $ticket->status = 'in_progress';
        }
        $ticket->save();

        return response()->json($message->load('user:id,name,avatar_url'));
    }

    public function updateStatus(Request $request, SupportTicket $ticket): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $ticket->update(['status' => $request->status]);

        return response()->json(['message' => 'Status updated', 'status' => $ticket->status]);
    }
}
