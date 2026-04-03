<?php
// FILE: c:\Users\Hello\Downloads\starjd\starjd.com\app\Notifications\CollaborationNotification.php

namespace App\Notifications;

use App\Models\Collaboration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class CollaborationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $collaboration;
    protected $event;
    protected $message;

    public function __construct(Collaboration $collaboration, string $event)
    {
        $this->collaboration = $collaboration;
        $this->event = $event;
        $this->message = $this->getMessageForEvent($event);
    }

    public function via($notifiable): array
    {
        return ['database']; // FCM is handled manually or via a custom channel if needed
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'collaboration',
            'collaboration_id' => $this->collaboration->id,
            'event' => $this->event,
            'message' => $this->message,
            'title' => 'Project Update',
        ];
    }

    public function sendPush($notifiable)
    {
        if (!$notifiable->fcm_token) return;

        try {
            $messaging = app('firebase.messaging');
            $notification = FirebaseNotification::create(
                'Project Update',
                $this->message
            );

            $fcmMessage = CloudMessage::withTarget('token', $notifiable->fcm_token)
                ->withNotification($notification)
                ->withData([
                    'type' => 'collaboration',
                    'collaboration_id' => (string)$this->collaboration->id,
                    'event' => $this->event,
                    'message' => $this->message,
                ]);

            $messaging->send($fcmMessage);
        } catch (\Exception $e) {
            \Log::error('FCM Error in CollaborationNotification: ' . $e->getMessage());
        }
    }

    protected function getMessageForEvent(string $event): string
    {
        switch ($event) {
            case 'accepted':
                return "Creator has accepted your collaboration request!";
            case 'paid':
                return "Brand has released the payment. You can start working now!";
            case 'delivered':
                return "Creator has delivered the project files. Please review them.";
            case 'revision_requested':
                return "Brand has requested a revision for your work.";
            case 'complete':
                return "Brand has approved your work. The project is completed!";
            case 'disputed':
                return "Brand has raised a dispute for the recent delivery. Admin will review soon.";
            case 'rejected':
                return "Your collaboration request has been declined by the creator.";
            case 'pending':
                return "Brand has resent the collaboration request. Please review it again.";
            default:
                return "Update on collaboration #" . $this->collaboration->id;
        }
    }
}
