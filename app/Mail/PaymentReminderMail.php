<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $couponCode;
    public $roleSlug;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, ?string $couponCode = null)
    {
        $this->user = $user;
        $this->couponCode = $couponCode;
        $this->roleSlug = $user->primaryRole()?->slug ?? $user->role;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $roleTitle = match ($this->roleSlug) {
            'creator' => 'Creator',
            'brand' => 'Brand',
            'studio_owner' => 'Studio Owner',
            'professional' => 'Professional',
            'agency' => 'Agency',
            default => 'Account',
        };

        $targetPath = match ($this->roleSlug) {
            'creator' => '/creator/choose-plan',
            'brand' => '/brand/choose-plan',
            'studio_owner' => '/studio/choose-plan',
            'professional' => '/professional/choose-plan',
            'agency' => '/agency/choose-plan',
            default => '/',
        };

        $paymentUrl = $this->authAwareUrl($targetPath);

        return $this->subject("Activate Your {$roleTitle} Account – Special Offer Inside")
                    ->markdown('emails.payment_reminder')
                    ->with([
                        'name' => $this->user->name,
                        'paymentUrl' => $paymentUrl,
                        'couponCode' => $this->couponCode,
                        'roleSlug' => $this->roleSlug,
                        'roleTitle' => $roleTitle,
                    ]);
    }

    private function authAwareUrl(string $targetPath): string
    {
        $normalized = '/' . ltrim($targetPath, '/');
        return url('/login?redirect=' . urlencode($normalized));
    }
}
?>
