<?php

namespace App\Providers;

use App\Models\Campaign;
use App\Observers\CampaignObserver;
use App\Observers\ReviewObserver;
use App\Services\PayUService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PayUService::class, function () {
            return PayUService::fromConfig();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Campaign::observe(CampaignObserver::class);


        // Password reset link points to SPA reset-password page
        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            $email = $notifiable->getEmailForPasswordReset();
            return url('/reset-password?token='.$token.'&email='.urlencode($email));
        });

        // Pinterest Socialite extension
        $socialite = $this->app->make(\Laravel\Socialite\Contracts\Factory::class);
        $socialite->extend('pinterest', function ($app) use ($socialite) {
            $config = $app['config']['services.pinterest'];
            return $socialite->buildProvider(\App\Socialite\PinterestProvider::class, $config);
        });
    }
}
