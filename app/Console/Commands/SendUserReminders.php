<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Mail\PaymentReminderMail;
use App\Mail\ProfileReminderMail;
use App\Mail\SocialConnectivityMail;
use App\Services\OnboardingService;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendUserReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:send-user-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Send scheduled email reminders for onboarding stages across all roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service = new OnboardingService();
        $now = Carbon::now();
        $users = User::whereNotNull('email_verified_at')->get();

        foreach ($users as $user) {
            // Stage 1 – Payment Reminder
            if (! $service->hasPaidAccess($user) && $user->last_payment_reminder_at === null) {
                // Ensure at least 24h after verification
                if ($user->email_verified_at->lt($now->subDay())) {
                    $coupon = $service->getApplicableCoupon($user);
                    Mail::to($user)->queue(new PaymentReminderMail($user, $coupon));
                    $user->update(['last_payment_reminder_at' => $now]);
                    continue; // move to next user after sending a reminder
                }
            }

            // Stage 2 – Profile Completion Reminder
            if ($service->hasPaidAccess($user) && ! $service->isProfileComplete($user) && $user->last_profile_reminder_at === null) {
                // Ensure at least 24h after payment (using created_at as proxy if payment date not stored)
                $payment = $user->accessPayments()->latest('paid_at')->first();
                $paymentDate = $payment ? $payment->paid_at : $user->created_at;
                if (Carbon::parse($paymentDate)->lt($now->subDay())) {
                    Mail::to($user)->queue(new ProfileReminderMail($user));
                    $user->update(['last_profile_reminder_at' => $now]);
                    continue;
                }
            }

            // Stage 3 – Social Connectivity Reminder (Creators only)
            if ($service->hasPaidAccess($user) && $service->isProfileComplete($user) && $user->last_social_reminder_at === null) {
                // Ensure at least 48h after profile completion (using updated_at as proxy)
                if ($user->updated_at->lt($now->subHours(48))) {
                    if (! $service->hasSocialConnected($user)) {
                        Mail::to($user)->queue(new SocialConnectivityMail($user));
                        $user->update(['last_social_reminder_at' => $now]);
                    }
                }
            }
        }

        $this->info('User reminders processed successfully.');
        return 0;
    }
}
?>
