<?php

namespace App\Providers;

use App\Models\Review;
use App\Observers\ReviewObserver;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Review::observe(ReviewObserver::class);

        // Password reset link points to SPA reset-password page
        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            $email = $notifiable->getEmailForPasswordReset();
            return url('/reset-password?token='.$token.'&email='.urlencode($email));
        });
    }
}
