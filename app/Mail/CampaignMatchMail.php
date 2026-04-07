<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampaignMatchMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $campaign;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $campaign)
    {
        $this->user = $user;
        $this->campaign = $campaign;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('New Campaign Matching Your Profile')
                    ->markdown('emails.campaign_match')
                    ->with([
                        'name' => $this->user->name,
                        'campaignTitle' => $this->campaign->title,
                        'campaignUrl' => url('/creator/campaigns/' . $this->campaign->slug),
                    ]);
    }
}
?>
