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

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, ?string $couponCode = null)
    {
        $this->user = $user;
        $this->couponCode = $couponCode;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Activate Your Account – Special Offer Inside')
                    ->markdown('emails.payment_reminder')
                    ->with([
                        'name' => $this->user->name,
                        'paymentUrl' => url('/payment'),
                        'couponCode' => $this->couponCode,
                    ]);
    }
}
?>
