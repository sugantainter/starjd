<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get paginated notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $notifications = $user->notifications()
                ->paginate($request->query('per_page', 20));

            // Format for JSON consumption
            $notifications->getCollection()->transform(function($n) {
                try {
                    $decodedData = is_string($n->data) ? json_decode($n->data, true) : $n->data;
                    return [
                        'id' => $n->id,
                        'type' => $decodedData['type'] ?? 'general',
                        'title' => $decodedData['title'] ?? 'Notification',
                        'description' => $decodedData['body'] ?? $decodedData['message'] ?? '',
                        'data' => $decodedData,
                        'read_at' => $n->read_at,
                        'created_at' => $n->created_at->toISOString(),
                    ];
                } catch (\Exception $e) {
                    return [
                        'id' => $n->id,
                        'type' => 'general',
                        'title' => 'System Notification',
                        'description' => 'The data for this notification could not be parsed.',
                        'read_at' => $n->read_at,
                        'created_at' => $n->created_at->toISOString(),
                    ];
                }
            });

            return response()->json($notifications);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading notifications: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read']);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'All notifications marked as read']);
    }

    /**
     * Get count of unread notifications.
     */
    public function unreadCount()
    {
        return response()->json([
            'count' => Auth::user()->unreadNotifications->count()
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json(['message' => 'Notification deleted']);
    }
}
