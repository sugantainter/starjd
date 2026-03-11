<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Get list of conversations (latest message from each user)
     */
    public function index(Request $request): JsonResponse
    {
        $userId = Auth::id();

        // Subquery to get latest message ID for each conversation
        $latestMessageIds = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->select(DB::raw('MAX(id) as id'))
            ->groupBy(DB::raw('CASE WHEN sender_id = ' . $userId . ' THEN receiver_id ELSE sender_id END'))
            ->pluck('id');

        $conversations = Message::whereIn('id', $latestMessageIds)
            ->with(['sender:id,name', 'receiver:id,name'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($message) use ($userId) {
                $otherUser = $message->sender_id == $userId ? $message->receiver : $message->sender;
                
                // Count unread messages from this user
                $unreadCount = Message::where('sender_id', $otherUser->id ?? 0)
                    ->where('receiver_id', $userId)
                    ->whereNull('read_at')
                    ->count();

                return [
                    'id' => $otherUser->id ?? 0,
                    'name' => $otherUser->name ?? 'Unknown',
                    'lastMessage' => $message->body,
                    'time' => $message->created_at->diffForHumans(),
                    'unreadCount' => $unreadCount,
                    'isOnline' => false, // Placeholder
                    'isBrand' => false, // Could check role
                    'avatar' => null, // Placeholder
                ];
            });

        return response()->json($conversations);
    }

    /**
     * Get messages for a specific user
     */
    public function show(Request $request, $otherUserId): JsonResponse
    {
        $userId = Auth::id();

        // Mark messages as read
        Message::where('sender_id', $otherUserId)
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::where(function ($query) use ($userId, $otherUserId) {
                $query->where('sender_id', $userId)->where('receiver_id', $otherUserId);
            })->orWhere(function ($query) use ($userId, $otherUserId) {
                $query->where('sender_id', $otherUserId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) use ($userId) {
                return [
                    'id' => $msg->id,
                    'isMe' => $msg->sender_id == $userId,
                    'text' => $msg->body,
                    'time' => $msg->created_at->format('h:i A'),
                    'type' => 'text',
                ];
            });

        return response()->json($messages);
    }

    /**
     * Send a new message
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'body' => 'required|string|max:5000',
            'thread_type' => 'nullable|string|in:campaign,booking,general',
            'thread_id' => 'nullable|integer|min:1',
        ]);

        $sender = Auth::user();

        $message = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $request->receiver_id,
            'body' => $request->body,
            'thread_type' => $request->thread_type,
            'thread_id' => $request->thread_id,
        ]);

        // Send Push Notification
        $receiver = User::find($request->receiver_id);
        if ($receiver && $receiver->fcm_token) {
            try {
                $messaging = app('firebase.messaging');
                $notification = \Kreait\Firebase\Messaging\Notification::create(
                    'New Message from ' . $sender->name,
                    $message->body
                );

                $fcmMessage = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $receiver->fcm_token)
                    ->withNotification($notification)
                    ->withData([
                        'type' => 'chat',
                        'sender_id' => (string)$sender->id,
                        'sender_name' => $sender->name,
                        'message_id' => (string)$message->id,
                        'body' => $message->body,
                    ]);

                $messaging->send($fcmMessage);
            } catch (\Exception $e) {
                \Log::error('FCM Error: ' . $e->getMessage());
            }
        }

        return response()->json([
            'id' => $message->id,
            'isMe' => true,
            'text' => $message->body,
            'time' => $message->created_at->format('h:i A'),
            'type' => 'text',
        ]);
    }
}
