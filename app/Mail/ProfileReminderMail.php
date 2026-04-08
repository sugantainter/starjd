<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProfileReminderMail extends Mailable implements ShouldQueue
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
        $roleTitle = match ($this->roleSlug) {
            'creator' => 'Creator',
            'brand' => 'Brand',
            'studio_owner' => 'Studio Owner',
            'professional' => 'Professional',
            'agency' => 'Agency',
            default => 'Account',
        };

        $targetPath = match ($this->roleSlug) {
            'creator' => '/creator/profile',
            'brand' => '/brand/profile',
            'studio_owner' => '/studio/studios',
            'professional' => '/professional/profile',
            'agency' => '/agency/dashboard',
            default => '/',
        };

        $profileUrl = $this->authAwareUrl($targetPath);

        return $this->subject("Complete Your {$roleTitle} Profile")
                    ->markdown('emails.profile_reminder')
                    ->with([
                        'name' => $this->user->name,
                        'profileUrl' => $profileUrl,
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
