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
        $ticket->load([
            'user:id,name,email', 
            'messages.user:id,name', 
            'collaboration.package',
            'collaboration.creator' => function($q) {
                $q->withCount([
                    'collaborationsAsCreator as collabs_count',
                    'collaborationsAsCreator as completed_count' => fn($q) => $q->where('status', 'completed'),
                    'collaborationsAsCreator as rejected_count' => fn($q) => $q->where('status', 'rejected'),
                ])->withSum('collaborationsAsCreator as total_revisions', 'revision_count');
            },
            'collaboration.brand' => function($q) {
                $q->withCount([
                    'collaborationsAsBrand as collabs_count',
                    'collaborationsAsBrand as rejected_count' => fn($q) => $q->where('status', 'rejected'),
                ]);
            }
        ]);
        $ticket->messages->each(fn ($message) => $message->user?->append('avatar_url'));

        return response()->json($ticket);
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

        $message->load('user:id,name');
        $message->user?->append('avatar_url');

        return response()->json($message);
    }

    public function updateStatus(Request $request, SupportTicket $ticket): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $ticket->update(['status' => $request->status]);

        return response()->json(['message' => 'Status updated', 'status' => $ticket->status]);
    }

    public function settle(Request $request, SupportTicket $ticket): JsonResponse
    {
        $ticket->load('collaboration');
        if (!$ticket->collaboration_id || !$ticket->collaboration) {
            return response()->json(['message' => 'No linked collaboration found'], 422);
        }

        $request->validate([
            'refund_brand' => 'required|numeric|min:0',
            'payout_creator' => 'required|numeric|min:0',
            'message' => 'nullable|string',
        ]);

        $collab = $ticket->collaboration;
        $totalAmount = (float) $collab->amount;

        if (round((float)$request->refund_brand + (float)$request->payout_creator, 2) > $totalAmount) {
             return response()->json(['message' => 'Total distribution exceeds project amount (₹' . $totalAmount . ')'], 422);
        }

        $collab->update([
            'status' => 'resolved',
            'resolved_refund_amount' => $request->refund_brand,
            'resolved_creator_amount' => $request->payout_creator,
        ]);

        // Add a message about the settlement
        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => "Mediation Resolution: Dispute has been settled.\n\nDistribution:\nRefund to Brand: ₹{$request->refund_brand}\nPayment to Creator: ₹{$request->payout_creator}\n\nNotes: " . ($request->message ?: 'Settled by Admin Mediation.'),
            'is_admin' => true,
        ]);

        $ticket->update(['status' => 'resolved']);

        return response()->json(['message' => 'Dispute settled and funds distributed.']);
    }
}
