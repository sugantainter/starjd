<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\MarketingCampaign;
use App\Channels\FcmChannel;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class MarketingNotification extends Notification
{
    use Queueable;

    protected $campaign;

    /**
     * Create a new notification instance.
     */
    public function __construct(MarketingCampaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];
        if ($this->campaign->type === 'email' || $this->campaign->type === 'both') {
            $channels[] = 'mail';
        }
        if (($this->campaign->type === 'push' || $this->campaign->type === 'both') && $notifiable->fcm_token) {
            $channels[] = FcmChannel::class;
        }
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject($this->campaign->title)
                    ->line($this->campaign->content)
                    ->action('View Website', url('/'))
                    ->line('Thank you for being part of our community!');
    }

    /**
     * Get the FCM representation of the notification.
     */
    public function toFcm(object $notifiable): CloudMessage
    {
        return CloudMessage::withTarget('token', $notifiable->fcm_token)
            ->withNotification(FirebaseNotification::create(
                $this->campaign->title,
                $this->campaign->content
            ))
            ->withData([
                'type' => 'marketing',
                'campaign_id' => (string)$this->campaign->id,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'campaign_id' => $this->campaign->id,
            'title' => $this->campaign->title,
        ];
    }
}
