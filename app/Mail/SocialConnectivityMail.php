<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SocialConnectivityMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $roleSlug;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->roleSlug = $user->primaryRole()?->slug ?? $user->role;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        [$subject, $targetPath, $ctaLabel] = match ($this->roleSlug) {
            'creator' => ['Connect Your Social Accounts', '/creator/social-accounts', 'Connect Social Accounts'],
            'brand' => ['Launch Your First Campaign', '/brand/dashboard', 'Create Campaign'],
            'studio_owner' => ['Set Your Studio Availability', '/studio/studios', 'Set Availability'],
            'professional' => ['Publish Your First Service', '/professional/services', 'Add Services'],
            'agency' => ['Complete Agency Onboarding', '/agency/dashboard', 'Continue Onboarding'],
            default => ['Complete Your Next Step', '/', 'Open Dashboard'],
        };

        $connectUrl = $this->authAwareUrl($targetPath);

        return $this->subject($subject)
                    ->markdown('emails.social_connectivity')
                    ->with([
                        'name' => $this->user->name,
                        'connectUrl' => $connectUrl,
                        'roleSlug' => $this->roleSlug,
                        'ctaLabel' => $ctaLabel,
                    ]);
    }

    private function authAwareUrl(string $targetPath): string
    {
        $normalized = '/' . ltrim($targetPath, '/');
        return url('/login?redirect=' . urlencode($normalized));
    }
}
?>
