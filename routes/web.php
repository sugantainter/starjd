<?php

/**
 * StarJD — Powered by Suganta International
 * https://starjd.com
 */

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CityController as AdminCityController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\LegalPageController as AdminLegalPageController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\StateController as AdminStateController;
use App\Http\Controllers\Admin\CountryController as AdminCountryController;
use App\Http\Controllers\Admin\StepController as AdminStepController;
use App\Http\Controllers\Admin\StudioCategoryController as AdminStudioCategoryController;
use App\Http\Controllers\Admin\AmenityController as AdminAmenityController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\StudioController as AdminStudioController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\HeroController as AdminHeroController;
use App\Http\Controllers\Admin\PartnerController as AdminPartnerController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\SupportAdminController;
use App\Http\Controllers\Admin\AIUsageController as AdminAIUsageController;
use App\Http\Controllers\Admin\PayoutRequestController as AdminPayoutRequestController;
use App\Http\Controllers\Admin\SitemapController;

use App\Http\Controllers\Api\AppConfigController;
use App\Http\Controllers\Api\PageController as ApiPageController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SectionsController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CreatorPublicController;
use App\Http\Controllers\StudioPublicController;
use App\Http\Controllers\AccessPaymentController;
use App\Http\Controllers\CollaborationController;
use App\Http\Controllers\Creator\CreatorCampaignApplicationController;
use App\Http\Controllers\Creator\CreatorController as CreatorDashboardController;
use App\Http\Controllers\Creator\CreatorProfileController;
use App\Http\Controllers\Creator\CreatorPackageController;
use App\Http\Controllers\Creator\CreatorSocialAccountController;
use App\Http\Controllers\Creator\CreatorImagePostController;
use App\Http\Controllers\Creator\CreatorFeaturedController;
use App\Http\Controllers\Creator\CreatorWalletController;
use App\Http\Controllers\CreatorOptionsController;
use App\Http\Controllers\Brand\BrandCampaignApplicationController;
use App\Http\Controllers\Brand\BrandCampaignController;
use App\Http\Controllers\Brand\BrandController as BrandDashboardController;
use App\Http\Controllers\Brand\BrandProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PayUController;
use App\Http\Controllers\StudioOwner\StudioOwnerBookingController;
use App\Http\Controllers\StudioOwner\StudioOwnerStudioController;
use App\Http\Controllers\StudioOwner\StudioOwnerStudioImageController;
use App\Http\Controllers\StudioOwner\StudioOwnerAvailabilityController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\Professional\AISuggestionController;
use App\Http\Controllers\BankAccountController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Storage files (when public/storage symlink is missing or broken, e.g. on Windows)
|--------------------------------------------------------------------------
*/
Route::get('/storage/{path}', \App\Http\Controllers\FileAccessController::class)->where('path', '.*')->name('storage.serve');

/*
|--------------------------------------------------------------------------
| XML sitemaps (generated under storage/app/sitemaps — no write permission on public/)
|--------------------------------------------------------------------------
*/
Route::get('/{sitemapFile}', [SitemapController::class, 'servePublic'])
    ->where('sitemapFile', 'sitemap\\.xml|sitemap_[0-9]+\\.xml');

/*
|--------------------------------------------------------------------------
| API Routes (SPA)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    Route::get('/app/config', [AppConfigController::class, 'config']);

    // Auth (guest)
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('auth/{provider}/token', [\App\Http\Controllers\SocialAuthController::class, 'apiCallback']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('register/creator', [AuthController::class, 'registerCreator']);
    Route::post('register/brand', [AuthController::class, 'registerBrand']);
    Route::post('register/agency', [AuthController::class, 'registerAgency']);
    Route::post('register/studio-owner', [AuthController::class, 'registerStudioOwner']);
    Route::post('register/customer', [AuthController::class, 'registerCustomer']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('email/verification-notification', [AuthController::class, 'resendVerification']);
    Route::post('verify-email-otp', [AuthController::class, 'verifyOtp']);
    Route::post('resend-otp', [AuthController::class, 'resendOtp']);
    Route::post('contact', [ContactController::class, 'store']);
    Route::get('sections', [SectionsController::class, 'index']);
    Route::get('posts', [PostController::class, 'index']);
    Route::get('posts/categories', [PostController::class, 'categories']);
    Route::get('posts/{slug}', [PostController::class, 'show']);
    Route::get('videos', [VideoController::class, 'index']);
    Route::get('shorts', [VideoController::class, 'shorts']);
    Route::get('creators', [CreatorPublicController::class, 'index']);
    Route::get('creators/options/filters', [CreatorOptionsController::class, 'filters']);
    Route::get('creators/{slug}', [CreatorPublicController::class, 'show']);
    Route::get('studios', [StudioPublicController::class, 'index']);
    Route::get('studios/categories', [StudioPublicController::class, 'categories']);
    Route::get('studios/{slugOrId}', [StudioPublicController::class, 'show']);
    Route::get('campaigns', [\App\Http\Controllers\Api\CampaignPublicController::class, 'index']);
    Route::get('campaigns/categories', [\App\Http\Controllers\Api\CampaignPublicController::class, 'categories']);
    Route::get('campaigns/filters', [\App\Http\Controllers\Api\CampaignPublicController::class, 'filters']);
    Route::get('campaigns/slug/{slug}', [\App\Http\Controllers\Api\CampaignPublicController::class, 'show']);
    Route::get('bookings/calculate', [BookingController::class, 'calculate']);
    Route::get('amenities', fn () => response()->json(\App\Models\Amenity::active()->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug', 'icon'])));
    Route::get('services', [ServiceController::class, 'index']);
    Route::get('services/{slug}', [ServiceController::class, 'show']);
    Route::get('pages', [ApiPageController::class, 'index']);
    Route::get('pages/{slug}', [ApiPageController::class, 'show']);
    Route::get('states', fn () => response()->json(\App\Models\State::orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug'])));
    Route::get('cities', function () {
        $stateId = request()->query('state_id');
        return response()->json(\App\Models\City::when($stateId, fn ($q) => $q->where('state_id', $stateId))->with('state:id,name,slug')->orderBy('sort_order')->orderBy('name')->get(['id', 'state_id', 'name', 'slug']));
    });
    Route::get('coupons', [\App\Http\Controllers\Api\CouponController::class, 'index']);
    Route::get('success-stories', [\App\Http\Controllers\Api\SuccessStoryController::class, 'index']);
    Route::get('success-stories/roles', [\App\Http\Controllers\Api\SuccessStoryController::class, 'roles']);
    Route::get('success-stories/{slug}', [\App\Http\Controllers\Api\SuccessStoryController::class, 'show']);

    Route::middleware(['auth:web'])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::apiResource('bank-accounts', BankAccountController::class)->only(['index', 'store', 'destroy']);
        Route::post('/ai-suggest/faqs', [AISuggestionController::class, 'generateFAQs']);
        Route::post('/ai-suggest/pricing', [AISuggestionController::class, 'generatePricing']);
        Route::post('/ai-suggest/generic', [AISuggestionController::class, 'suggest']);
        Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
            ->middleware('signed')
            ->name('verification.verify');
        Route::post('bookings', [BookingController::class, 'store']);
        Route::post('bookings/confirm', [BookingController::class, 'confirm']);
        Route::get('payment/plans', [AccessPaymentController::class, 'plans']);
        Route::get('payment/status', [AccessPaymentController::class, 'status']);
        Route::post('payment/pay', [AccessPaymentController::class, 'pay']);
        Route::get('collaborations', [CollaborationController::class, 'index']);
        Route::post('collaborations', [CollaborationController::class, 'store']);
        Route::get('conversations', [\App\Http\Controllers\Api\MessageController::class, 'index']);
        Route::get('messages/unread-count', [\App\Http\Controllers\Api\MessageController::class, 'unreadCount']);
        Route::get('messages', [\App\Http\Controllers\Api\MessageController::class, 'index']);
        
        // Notifications
        Route::get('notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
        Route::get('notifications/unread-count', [\App\Http\Controllers\Api\NotificationController::class, 'unreadCount']);
        Route::post('notifications/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
        Route::post('notifications/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
        Route::delete('notifications/{id}', [\App\Http\Controllers\Api\NotificationController::class, 'destroy']);
        Route::get('messages/{otherUserId}', [\App\Http\Controllers\Api\MessageController::class, 'show']);
        Route::post('messages', [\App\Http\Controllers\Api\MessageController::class, 'store']);
        Route::post('collaborations/{collaboration}/accept', [CollaborationController::class, 'accept']);
        Route::post('collaborations/{collaboration}/reject', [CollaborationController::class, 'reject']);
        Route::post('collaborations/{collaboration}/resend', [CollaborationController::class, 'resend']);
        Route::post('collaborations/{collaboration}/pay', [CollaborationController::class, 'pay']);
        Route::post('collaborations/{collaboration}/revision', [CollaborationController::class, 'requestRevision']);
        Route::post('collaborations/{collaboration}/deliver', [CollaborationController::class, 'deliver']);
        Route::post('collaborations/{collaboration}/complete', [CollaborationController::class, 'complete']);
        Route::post('collaborations/{collaboration}/reject-delivery', [CollaborationController::class, 'rejectDelivery']);
        Route::get('collaborations/{collaboration}/file/stream', [CollaborationController::class, 'streamDeliverable']);
        Route::get('collaborations/{collaboration}/file', [CollaborationController::class, 'previewFile']);
        Route::post('payment/payu/create', [PayUController::class, 'create']);

        Route::get('payment/coupon/validate', [PayUController::class, 'validateCoupon']);
        Route::post('collaborations/{collaboration}/claim-settlement', [CollaborationController::class, 'claimSettlement']);
    });

    Route::middleware(['auth:web', 'verified', 'creator', 'paid'])->prefix('creator')->group(function () {
        Route::get('dashboard', [CreatorDashboardController::class, 'dashboard']);
        Route::get('profile', [CreatorProfileController::class, 'show']);
        Route::put('profile', [CreatorProfileController::class, 'update']);
        Route::post('profile', [CreatorProfileController::class, 'update']); // POST required when uploading avatar (PHP does not populate $_FILES for PUT)
        Route::get('package-categories', [CreatorPackageController::class, 'categories']);
        Route::apiResource('packages', CreatorPackageController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('social-accounts', [CreatorSocialAccountController::class, 'index']);
        Route::post('social-accounts/sync', [CreatorSocialAccountController::class, 'sync']);
        Route::post('social-accounts/{platform}/refresh', [CreatorSocialAccountController::class, 'refresh']);
        Route::delete('social-accounts/{platform}', [CreatorSocialAccountController::class, 'disconnect']);
        Route::get('image-posts', [CreatorImagePostController::class, 'index']);
        Route::post('image-posts', [CreatorImagePostController::class, 'store']);
        Route::delete('image-posts/{creatorImagePost}', [CreatorImagePostController::class, 'destroy']);
        Route::get('featured-plans', [CreatorFeaturedController::class, 'plans']);
        Route::get('featured/status', [CreatorFeaturedController::class, 'status']);
        Route::post('featured/purchase', [CreatorFeaturedController::class, 'purchase']);
        Route::get('wallet', [CreatorWalletController::class, 'show']);
        Route::get('wallet/transactions', [CreatorWalletController::class, 'transactions']);
        Route::get('campaign-applications', [CreatorCampaignApplicationController::class, 'index']);
        Route::post('campaign-applications', [CreatorCampaignApplicationController::class, 'store']);
    });

    Route::middleware(['auth:web', 'verified', 'brand', 'paid'])->prefix('brand')->group(function () {
        Route::get('dashboard', [BrandDashboardController::class, 'dashboard']);
        Route::get('profile', [BrandProfileController::class, 'show']);
        Route::put('profile', [BrandProfileController::class, 'update']);
        Route::post('profile', [BrandProfileController::class, 'update']); // POST required when uploading logo (PHP does not populate $_FILES for PUT)
        Route::get('campaigns', [BrandCampaignController::class, 'index']);
        Route::get('campaigns/{campaign}', [BrandCampaignController::class, 'show']);
        Route::post('campaigns', [BrandCampaignController::class, 'store']);
        Route::put('campaigns/{campaign}', [BrandCampaignController::class, 'update']);
        Route::patch('campaigns/{campaign}', [BrandCampaignController::class, 'update']);
        Route::patch('campaign-applications/{campaign_application}', [BrandCampaignApplicationController::class, 'update']);
    });

    Route::middleware(['auth:web', 'verified', 'agency'])->prefix('agency')->group(function () {
        Route::get('dashboard', fn () => response()->json(['message' => 'Agency dashboard', 'user' => request()->user()->only('id', 'name', 'email')]));
    });

    // Studio owner: allow access without email verification (avoid redirect loop; show verify banner in UI if needed)
    Route::middleware(['auth:web', 'studio_owner'])->prefix('studio')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\StudioOwner\StudioOwnerController::class, 'dashboard']);
    });
    Route::middleware(['auth:web', 'studio_owner'])->prefix('studio-owner')->group(function () {
        Route::get('studios', [StudioOwnerStudioController::class, 'index']);
        Route::get('studios/{studio}', [StudioOwnerStudioController::class, 'show']);
        Route::post('studios', [StudioOwnerStudioController::class, 'store']);
        Route::put('studios/{studio}', [StudioOwnerStudioController::class, 'update']);
        Route::delete('studios/{studio}', [StudioOwnerStudioController::class, 'destroy']);
        Route::post('studios/{studio}/images', [StudioOwnerStudioImageController::class, 'store']);
        Route::put('studios/{studio}/images/reorder', [StudioOwnerStudioImageController::class, 'reorder']);
        Route::delete('studio-images/{studio_image}', [StudioOwnerStudioImageController::class, 'destroy']);
        Route::get('studios/{studio}/availability', [StudioOwnerAvailabilityController::class, 'index']);
        Route::post('studios/{studio}/availability', [StudioOwnerAvailabilityController::class, 'store']);
        Route::put('availability-slots/{availability_slot}', [StudioOwnerAvailabilityController::class, 'update']);
        Route::delete('availability-slots/{availability_slot}', [StudioOwnerAvailabilityController::class, 'destroy']);
        Route::get('bookings', [StudioOwnerBookingController::class, 'index']);
    });

    Route::middleware(['auth:web', 'verified', 'admin'])->prefix('admin')->group(function () {
        Route::get('payout-requests', [AdminPayoutRequestController::class, 'index']);
        Route::post('payout-requests/{payoutRequest}/process', [AdminPayoutRequestController::class, 'process']);
        Route::get('user', fn () => response()->json(request()->user()));
        Route::get('dashboard', [DashboardController::class, 'index']);
        Route::get('roles', fn () => response()->json(\App\Models\Role::whereNotIn('slug', ['admin', 'customer'])->orderBy('name')->get(['id', 'name', 'slug'])));
        Route::apiResource('categories', AdminCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('testimonials', AdminTestimonialController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('faqs', AdminFaqController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('steps', AdminStepController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('contacts', [ContactMessageController::class, 'index']);
        Route::delete('contacts/{id}', [ContactMessageController::class, 'destroy'])->name('admin.contacts.destroy');
        Route::post('posts/upload', [AdminPostController::class, 'uploadImage']);
        Route::get('storage-url', [\App\Http\Controllers\Admin\StorageUrlController::class, 'show']);
        Route::apiResource('posts', AdminPostController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('videos', AdminVideoController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('hero', [AdminHeroController::class, 'show']);
        Route::put('hero', [AdminHeroController::class, 'update']);
        Route::post('hero/upload', [AdminHeroController::class, 'upload']);
        Route::apiResource('partners', AdminPartnerController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::post('partners/upload', [AdminPartnerController::class, 'upload']);
        Route::apiResource('services', AdminServiceController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('countries', [AdminCountryController::class, 'index']);
        Route::apiResource('states', AdminStateController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('cities', AdminCityController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('pages', AdminPageController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('legal-pages', [AdminLegalPageController::class, 'index']);
        Route::put('legal-pages/{slug}', [AdminLegalPageController::class, 'update']);
        Route::get('studio-owners', [AdminStudioController::class, 'studioOwners']);
        Route::get('studios', [AdminStudioController::class, 'index']);
        Route::post('studios', [AdminStudioController::class, 'store']);
        Route::get('studios/{studio}', [AdminStudioController::class, 'show']);
        Route::put('studios/{studio}', [AdminStudioController::class, 'update']);
        Route::apiResource('studio-categories', AdminStudioCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('amenities', AdminAmenityController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('coupons', AdminCouponController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('banners', \App\Http\Controllers\Admin\BannerController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::post('banners/upload', [\App\Http\Controllers\Admin\BannerController::class, 'upload']);
        Route::post('success-stories/upload', [\App\Http\Controllers\Admin\SuccessStoryController::class, 'uploadImage']);
        Route::apiResource('success-stories', \App\Http\Controllers\Admin\SuccessStoryController::class);
        
        // Support Tickets
        Route::get('support/tickets', [SupportAdminController::class, 'index']);
        Route::get('support/tickets/{ticket}', [SupportAdminController::class, 'show']);
        Route::post('support/tickets/{ticket}/reply', [SupportAdminController::class, 'reply']);
        Route::patch('support/tickets/{ticket}/status', [SupportAdminController::class, 'updateStatus']);
        Route::post('support/tickets/{ticket}/settle', [SupportAdminController::class, 'settle']);

        // AI Usage
        Route::get('ai/usage', [AdminAIUsageController::class, 'index']);
        Route::get('ai/usage/users', [AdminAIUsageController::class, 'userStats']);
        Route::get('ai/usage/logs', [AdminAIUsageController::class, 'recentLogs']);

        // Marketing
        Route::get('marketing/stats', [\App\Http\Controllers\Admin\MarketingController::class, 'stats']);
        Route::get('marketing/filters', [\App\Http\Controllers\Admin\MarketingController::class, 'getFilters']);
        Route::apiResource('marketing', \App\Http\Controllers\Admin\MarketingController::class)->only(['index', 'store', 'show']);
        Route::post('marketing/{marketing}/send', [\App\Http\Controllers\Admin\MarketingController::class, 'send']);

        // Users
        Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index']);
        Route::get('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show']);
        Route::patch('users/{user}/status', [\App\Http\Controllers\Admin\UserController::class, 'updateStatus']);

        // Sitemap
        Route::get('sitemap', [SitemapController::class, 'status']);
        Route::post('sitemap/generate', [SitemapController::class, 'generate']);
    });
});

/*
|--------------------------------------------------------------------------
| Social / OAuth login & registration (must be before SPA catch-all)
|--------------------------------------------------------------------------
*/
Route::match(['get', 'post'], '/payment/callback/success', [PayUController::class, 'callback'])->name('payu.callback.success');
Route::match(['get', 'post'], '/payment/callback/failure', [PayUController::class, 'callback'])->name('payu.callback.failure');

Route::get('/auth/google/redirect', [SocialAuthController::class, 'googleRedirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [SocialAuthController::class, 'googleCallback'])->name('auth.google.callback');
Route::get('/auth/facebook/redirect', [SocialAuthController::class, 'facebookRedirect'])->name('auth.facebook.redirect');
Route::get('/auth/facebook/callback', [SocialAuthController::class, 'facebookCallback'])->name('auth.facebook.callback');

// Creator Account Connections (OAuth)
Route::middleware(['auth:web', 'verified', 'creator'])->group(function () {
    Route::get('/creator/social-accounts/{platform}/redirect', [CreatorSocialAccountController::class, 'redirect'])->name('creator.social.redirect');
    Route::get('/creator/social-accounts/{platform}/callback', [CreatorSocialAccountController::class, 'callback'])->name('creator.social.callback');
});

/*
|--------------------------------------------------------------------------
| Web Routes (SPA fallback)
|--------------------------------------------------------------------------
| All front-end routes are handled by the Vue SPA, including: /, /about,
| /contact, /privacy, /terms, /child-safety, /blog, /services, /creators, /login, /register,
| /creator/*, /brand/*, /admin/*, etc.
*/
Route::get('/login', [HomeController::class, 'index'])->name('login');

// Legacy CMS URLs: /page/{slug} → /{slug} (301; canonical is root path, e.g. /influencers-in-ahmedabad)
Route::get('/page/{slug}', function (string $slug) {
    return redirect('/'.$slug, 301);
})->where('slug', '[^/]+');

Route::get('/{any?}', [HomeController::class, 'index'])->where('any', '.*')->name('home');
