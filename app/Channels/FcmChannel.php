<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FcmChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification): void
    {
        \Illuminate\Support\Facades\Log::info("FcmChannel@send starting", ['user_id' => $notifiable->id]);
        if (!method_exists($notification, 'toFcm')) {
            return;
        }

        $message = $notification->toFcm($notifiable);

        if (!$message instanceof CloudMessage) {
            return;
        }

        try {
            Firebase::messaging()->send($message);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("FCM Send Error: " . $e->getMessage());
        }
    }
}
