<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    public function index(): JsonResponse
    {
        $userId = auth()->id();
        
        $tickets = SupportTicket::where('user_id', $userId)
            ->orWhereHas('collaboration', function ($q) use ($userId) {
                $q->where('creator_id', $userId);
            })
            ->withCount('messages')
            ->orderByDesc('updated_at')
            ->get();

        return response()->json($tickets);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => $request->user()->id,
            'ticket_id' => 'TKT-' . strtoupper(Str::random(8)),
            'subject' => $request->subject,
            'priority' => $request->priority ?? 'medium',
            'status' => 'open',
        ]);

        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'message' => $request->message,
            'is_admin' => false,
        ]);

        return response()->json([
            'message' => 'Ticket raised successfully',
            'ticket' => $ticket->load('messages'),
        ]);
    }

    public function show(SupportTicket $ticket): JsonResponse
    {
        $userId = auth()->id();
        $isCreator = $ticket->collaboration && $ticket->collaboration->creator_id === $userId;

        if ($ticket->user_id !== $userId && !$isCreator) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $ticket->load(['messages.user:id,name', 'collaboration.package']);
        $ticket->messages->each(fn ($message) => $message->user?->append('avatar_url'));

        return response()->json($ticket);
    }

    public function sendMessage(Request $request, SupportTicket $ticket): JsonResponse
    {
        $userId = $request->user()->id;
        $isCreator = $ticket->collaboration && $ticket->collaboration->creator_id === $userId;

        if ($ticket->user_id !== $userId && !$isCreator) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (in_array($ticket->status, ['closed', 'resolved'])) {
            return response()->json(['message' => 'This ticket is closed and cannot be replied to. Please raise a new ticket if needed.'], 403);
        }

        $request->validate([
            'message' => 'required|string',
        ]);

        $message = SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'message' => $request->message,
            'is_admin' => false,
        ]);

        $ticket->touch(); // update updated_at

        $message->load('user:id,name');
        $message->user?->append('avatar_url');

        return response()->json($message);
    }
}
