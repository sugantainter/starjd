<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailOtpNotification extends Notification
{

    public function __construct(
        public string $otp
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appName = config('app.name', 'StarJD');

        return (new MailMessage)
            ->subject("Your {$appName} verification code")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your email verification code is: **{$this->otp}**")
            ->line('This code is valid for 10 minutes.')
            ->line('If you did not request this, you can ignore this email.')
            ->salutation('Thank you, ' . $appName);
    }
}
