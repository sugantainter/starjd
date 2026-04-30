<?php

use App\Http\Controllers\Api\AppConfigController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SectionsController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\StudioPublicController;
use App\Http\Controllers\CreatorPublicController;
use App\Http\Controllers\CreatorOptionsController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\SupportController;
use App\Http\Controllers\BrandPublicController;
use App\Http\Controllers\CampaignApplicationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\CampaignPublicController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\LocationCmsController;
use App\Http\Controllers\LocationPublicController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes (Stateless - No CSRF required)
|--------------------------------------------------------------------------
| Automatically prefixed with /api. No session/CSRF middleware.
| */

// App config
Route::get('/app/config', [AppConfigController::class, 'config']);
Route::get('/faqs', [FaqController::class, 'index']);


// ── Public content ─────────────────────────────────────────────────────────
Route::get('sections',              [SectionsController::class, 'index']);

// Blogs / Posts
Route::get('posts',                 [PostController::class, 'index']);
Route::get('posts/categories',      [PostController::class, 'categories']);
Route::get('posts/{slug}',          [PostController::class, 'show']);

// Services
Route::get('services',              [ServiceController::class, 'index']);
Route::get('services/{slug}',       [ServiceController::class, 'show']);

// Videos / Shorts
Route::get('videos',                [VideoController::class, 'index']);
Route::get('shorts',                [VideoController::class, 'shorts']);

// Studios – Public
Route::get('studios',               [StudioPublicController::class, 'index']);
Route::get('studios/categories',    [StudioPublicController::class, 'categories']);
Route::get('studios/{slugOrId}',    [StudioPublicController::class, 'show']);

// Creators – Public
Route::get('creators',                  [CreatorPublicController::class, 'index']);
Route::get('creators/options/filters',  [CreatorOptionsController::class, 'filters']);
Route::get('creators/{slug}',           [CreatorPublicController::class, 'show']);

// Brands – Public
Route::get('brands',                    [BrandPublicController::class, 'index']);
Route::get('brands/{slug}',              [BrandPublicController::class, 'show']);
Route::get('partners',                   [\App\Http\Controllers\Admin\PartnerController::class, 'index']);

// Campaigns – Public
Route::get('campaigns',                 [CampaignPublicController::class, 'index']);
Route::get('campaigns/filters',         [CampaignPublicController::class, 'filters']);
Route::get('campaigns/categories',      [CampaignPublicController::class, 'categories']);
Route::get('campaigns/{slug}',          [CampaignPublicController::class, 'show']);

// Professional Services (Gigs) – Public
Route::get('gigs',                      [\App\Http\Controllers\ProfessionalPublicController::class, 'index']);
Route::get('gigs/{slug}',               [\App\Http\Controllers\ProfessionalPublicController::class, 'show']);
Route::get('professionals/{id}',        [\App\Http\Controllers\ProfessionalPublicController::class, 'professionalProfile']);

// Misc lookups
Route::get('amenities', fn () => response()->json(
    \App\Models\Amenity::active()->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug', 'icon'])
));
Route::get('states', fn () => response()->json(
    \App\Models\State::orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug'])
));
Route::get('cities', function () {
    $stateId = request()->query('state_id');
    return response()->json(
        \App\Models\City::when($stateId, fn ($q) => $q->where('state_id', $stateId))
            ->with('state:id,name,slug')->orderBy('sort_order')->orderBy('name')
            ->get(['id', 'state_id', 'name', 'slug'])
    );
});
Route::get('pages', [PageController::class, 'index']);
Route::get('pages/{slug}', [PageController::class, 'show']);
Route::get('seo-content/{slug}', [LocationPublicController::class, 'show']);

// ── Auth ───────────────────────────────────────────────────────────────────
Route::post('login',                        [AuthController::class, 'login']);
Route::post('logout',                       [AuthController::class, 'logout']);
Route::post('auth/{provider}/token',        [SocialAuthController::class, 'apiCallback']);
Route::post('register',                     [AuthController::class, 'register']);
Route::post('register/creator',             [AuthController::class, 'registerCreator']);
Route::post('register/brand',               [AuthController::class, 'registerBrand']);
Route::post('register/agency',              [AuthController::class, 'registerAgency']);
Route::post('register/studio-owner',        [AuthController::class, 'registerStudioOwner']);
Route::post('register/customer',            [AuthController::class, 'registerCustomer']);
Route::post('forgot-password',              [AuthController::class, 'forgotPassword']);
Route::post('reset-password',               [AuthController::class, 'resetPassword']);
Route::post('email/verification-notification', [AuthController::class, 'resendVerification']);
Route::post('verify-email-otp',             [AuthController::class, 'verifyOtp']);
Route::post('resend-otp',                   [AuthController::class, 'resendOtp']);
Route::post('contact',                      [ContactController::class, 'store']);

// ── Mobile App Stateful Endpoints (session cookies required) ─────────────────
Route::middleware('web')->group(function () {
    Route::post('mobile-register', [AuthController::class, 'mobileRegister']);

    Route::middleware('auth:web')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::delete('account', [AuthController::class, 'deleteAccount']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        Route::post('update-profile', [AuthController::class, 'updateProfile']);
        Route::post('set-role', [AuthController::class, 'setRole']);
        Route::post('update-fcm-token', [AuthController::class, 'updateFcmToken']);
        
        // Chat
        Route::get('conversations', [MessageController::class, 'index']);
        Route::get('messages/{userId}', [MessageController::class, 'show']);
        Route::post('messages', [MessageController::class, 'store']);
        
        // Support Tickets
        Route::get('support/tickets', [SupportController::class, 'index']);
        Route::post('support/tickets', [SupportController::class, 'store']);
        Route::get('support/tickets/{ticket}', [SupportController::class, 'show']);
        Route::post('support/tickets/{ticket}/messages', [SupportController::class, 'sendMessage']);

        
        // Onboarding / Profile updates
        Route::post('creator/onboarding', [\App\Http\Controllers\Creator\CreatorProfileController::class, 'update']);
        Route::post('brand/onboarding', [\App\Http\Controllers\Brand\BrandProfileController::class, 'update']);

    // Creator profile editor (used by Vue: /api/creator/profile)
    Route::get('creator/profile', [\App\Http\Controllers\Creator\CreatorProfileController::class, 'show']);
    Route::put('creator/profile', [\App\Http\Controllers\Creator\CreatorProfileController::class, 'update']);
    Route::post('creator/profile', [\App\Http\Controllers\Creator\CreatorProfileController::class, 'update']);

        // Dashboard Data (Mirroring web routes but for API)
        Route::middleware(['creator', 'paid'])->prefix('creator')->group(function () {
            Route::get('dashboard', [\App\Http\Controllers\Creator\CreatorController::class, 'dashboard']);
            Route::get('packages', [\App\Http\Controllers\Creator\CreatorPackageController::class, 'index']);
            Route::get('packages/categories', [\App\Http\Controllers\Creator\CreatorPackageController::class, 'categories']);
            Route::post('packages', [\App\Http\Controllers\Creator\CreatorPackageController::class, 'store']);
            Route::put('packages/{package}', [\App\Http\Controllers\Creator\CreatorPackageController::class, 'update']);
            Route::delete('packages/{package}', [\App\Http\Controllers\Creator\CreatorPackageController::class, 'destroy']);
            Route::get('image-posts', [\App\Http\Controllers\Creator\CreatorImagePostController::class, 'index']);
            Route::post('image-posts', [\App\Http\Controllers\Creator\CreatorImagePostController::class, 'store']);
            
            Route::get('social-accounts', [\App\Http\Controllers\Creator\CreatorSocialAccountController::class, 'index']);
            Route::post('social-accounts/sync', [\App\Http\Controllers\Creator\CreatorSocialAccountController::class, 'sync']);
            Route::post('social-accounts/{platform}/refresh', [\App\Http\Controllers\Creator\CreatorSocialAccountController::class, 'refresh']);
            Route::post('social-accounts/{platform}/select-page', [\App\Http\Controllers\Creator\CreatorSocialAccountController::class, 'selectPage']);
            Route::post('social-accounts/{platform}/select-instagram', [\App\Http\Controllers\Creator\CreatorSocialAccountController::class, 'selectInstagram']);
            Route::delete('social-accounts/{platform}', [\App\Http\Controllers\Creator\CreatorSocialAccountController::class, 'disconnect']);

            // Campaigns
            Route::get('campaign-applications', [\App\Http\Controllers\Creator\CreatorCampaignApplicationController::class, 'index']);
            Route::post('campaign-applications', [\App\Http\Controllers\Creator\CreatorCampaignApplicationController::class, 'store']);
        });

        Route::middleware(['brand', 'paid'])->prefix('brand')->group(function () {
            Route::get('dashboard', [\App\Http\Controllers\Brand\BrandController::class, 'dashboard']);
            Route::get('campaigns', [\App\Http\Controllers\Brand\BrandCampaignController::class, 'index']);
            Route::get('campaigns/{campaign}', [\App\Http\Controllers\Brand\BrandCampaignController::class, 'show']);
            Route::post('campaigns', [\App\Http\Controllers\Brand\BrandCampaignController::class, 'store']);
            Route::put('campaigns/{campaign}', [\App\Http\Controllers\Brand\BrandCampaignController::class, 'update']);
            Route::delete('campaigns/{campaign}', [\App\Http\Controllers\Brand\BrandCampaignController::class, 'destroy']);
            Route::patch('campaign-applications/{campaign_application}', [\App\Http\Controllers\Brand\BrandCampaignApplicationController::class, 'update']);
        });

        Route::middleware(['studio_owner', 'paid'])->prefix('studio-owner')->group(function () {
            Route::get('dashboard', [\App\Http\Controllers\StudioOwner\StudioOwnerController::class, 'dashboard']);
            Route::get('studios', [\App\Http\Controllers\StudioOwner\StudioOwnerStudioController::class, 'index']);
            Route::get('studios/{studio}', [\App\Http\Controllers\StudioOwner\StudioOwnerStudioController::class, 'show']);
            Route::post('studios', [\App\Http\Controllers\StudioOwner\StudioOwnerStudioController::class, 'store']);
            Route::put('studios/{studio}', [\App\Http\Controllers\StudioOwner\StudioOwnerStudioController::class, 'update']);
            Route::delete('studios/{studio}', [\App\Http\Controllers\StudioOwner\StudioOwnerStudioController::class, 'destroy']);
            Route::get('bookings', [\App\Http\Controllers\StudioOwner\StudioOwnerBookingController::class, 'index']);
            
            Route::get('studios/{studio}/availability', [\App\Http\Controllers\StudioOwner\StudioOwnerAvailabilityController::class, 'index']);
            Route::post('studios/{studio}/availability', [\App\Http\Controllers\StudioOwner\StudioOwnerAvailabilityController::class, 'store']);
            Route::put('availability-slots/{availability_slot}', [\App\Http\Controllers\StudioOwner\StudioOwnerAvailabilityController::class, 'update']);
            Route::delete('availability-slots/{availability_slot}', [\App\Http\Controllers\StudioOwner\StudioOwnerAvailabilityController::class, 'destroy']);
        });

        Route::middleware(['professional', 'paid'])->prefix('professional')->group(function () {
            Route::get('dashboard', [\App\Http\Controllers\Professional\ProfessionalController::class, 'dashboard']);
            Route::post('profile', [\App\Http\Controllers\Professional\ProfessionalController::class, 'updateProfile']);
            Route::get('categories', [\App\Http\Controllers\Professional\ProfessionalController::class, 'categories']);
            Route::get('listings', [\App\Http\Controllers\Professional\ProfessionalController::class, 'listings']);
            Route::post('listings', [\App\Http\Controllers\Professional\ProfessionalController::class, 'storeListing']);
            Route::post('upload-image', [\App\Http\Controllers\Professional\ProfessionalController::class, 'uploadImage']);
            Route::post('ai/suggest-title', [\App\Http\Controllers\Professional\AISuggestionController::class, 'suggestTitle']);
            Route::post('ai/suggest-description', [\App\Http\Controllers\Professional\AISuggestionController::class, 'suggestDescription']);
            Route::post('ai/suggest-tags', [\App\Http\Controllers\Professional\AISuggestionController::class, 'suggestTags']);
            Route::post('ai/suggest-pricing', [\App\Http\Controllers\Professional\AISuggestionController::class, 'suggestPricing']);
            Route::post('ai/suggest-faqs', [\App\Http\Controllers\Professional\AISuggestionController::class, 'suggestFAQs']);
            Route::get('orders', [\App\Http\Controllers\Professional\ProfessionalController::class, 'orders']);
        });

        Route::middleware(['auth:web', 'verified', 'admin'])->prefix('admin')->group(function () {
            Route::get('payout-requests', [\App\Http\Controllers\Admin\PayoutRequestController::class, 'index']);
            Route::post('payout-requests/{payoutRequest}/process', [\App\Http\Controllers\Admin\PayoutRequestController::class, 'process']);
            Route::get('user', fn () => response()->json(request()->user()));
            Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index']);
            Route::get('roles', fn () => response()->json(\App\Models\Role::whereNotIn('slug', ['admin', 'customer'])->orderBy('name')->get(['id', 'name', 'slug'])));
            Route::apiResource('categories', \App\Http\Controllers\Admin\CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::apiResource('sub-categories', \App\Http\Controllers\Admin\SubCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::apiResource('testimonials', \App\Http\Controllers\Admin\TestimonialController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::apiResource('faqs', \App\Http\Controllers\Admin\FaqController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::apiResource('steps', \App\Http\Controllers\Admin\StepController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::get('contacts', [\App\Http\Controllers\Admin\ContactMessageController::class, 'index']);
            Route::delete('contacts/{id}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])->name('admin.contacts.destroy');
            Route::post('posts/upload', [\App\Http\Controllers\Admin\PostController::class, 'uploadImage']);
            Route::get('storage-url', [\App\Http\Controllers\Admin\StorageUrlController::class, 'show']);
            Route::apiResource('posts', \App\Http\Controllers\Admin\PostController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::apiResource('videos', \App\Http\Controllers\Admin\VideoController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::get('hero', [\App\Http\Controllers\Admin\HeroController::class, 'show']);
            Route::put('hero', [\App\Http\Controllers\Admin\HeroController::class, 'update']);
            Route::post('hero/upload', [\App\Http\Controllers\Admin\HeroController::class, 'upload']);
            Route::apiResource('partners', \App\Http\Controllers\Admin\PartnerController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::post('partners/upload', [\App\Http\Controllers\Admin\PartnerController::class, 'upload']);
            Route::apiResource('services', \App\Http\Controllers\Admin\ServiceController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::get('countries', [\App\Http\Controllers\Admin\CountryController::class, 'index']);
            Route::apiResource('states', \App\Http\Controllers\Admin\StateController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::apiResource('cities', \App\Http\Controllers\Admin\CityController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::apiResource('pages', \App\Http\Controllers\Admin\PageController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::get('legal-pages', [\App\Http\Controllers\Admin\LegalPageController::class, 'index']);
            Route::put('legal-pages/{slug}', [\App\Http\Controllers\Admin\LegalPageController::class, 'update']);
            Route::get('studio-owners', [\App\Http\Controllers\Admin\StudioController::class, 'studioOwners']);
            Route::get('studios', [\App\Http\Controllers\Admin\StudioController::class, 'index']);
            Route::post('studios', [\App\Http\Controllers\Admin\StudioController::class, 'store']);
            Route::get('studios/{studio}', [\App\Http\Controllers\Admin\StudioController::class, 'show']);
            Route::put('studios/{studio}', [\App\Http\Controllers\Admin\StudioController::class, 'update']);
            Route::apiResource('studio-categories', \App\Http\Controllers\Admin\StudioCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::apiResource('amenities', \App\Http\Controllers\Admin\AmenityController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::apiResource('coupons', \App\Http\Controllers\Admin\CouponController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::apiResource('banners', \App\Http\Controllers\Admin\BannerController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::post('banners/upload', [\App\Http\Controllers\Admin\BannerController::class, 'upload']);
            Route::post('success-stories/upload', [\App\Http\Controllers\Admin\SuccessStoryController::class, 'uploadImage']);
            Route::apiResource('success-stories', \App\Http\Controllers\Admin\SuccessStoryController::class);
            
            // Support Tickets
            Route::get('support/tickets', [\App\Http\Controllers\Admin\SupportAdminController::class, 'index']);
            Route::get('support/tickets/{ticket}', [\App\Http\Controllers\Admin\SupportAdminController::class, 'show']);
            Route::post('support/tickets/{ticket}/reply', [\App\Http\Controllers\Admin\SupportAdminController::class, 'reply']);
            Route::patch('support/tickets/{ticket}/status', [\App\Http\Controllers\Admin\SupportAdminController::class, 'updateStatus']);
            Route::post('support/tickets/{ticket}/settle', [\App\Http\Controllers\Admin\SupportAdminController::class, 'settle']);

            // AI Usage
            Route::get('ai/usage', [\App\Http\Controllers\Admin\AIUsageController::class, 'index']);
            Route::get('ai/usage/users', [\App\Http\Controllers\Admin\AIUsageController::class, 'userStats']);
            Route::get('ai/usage/logs', [\App\Http\Controllers\Admin\AIUsageController::class, 'recentLogs']);

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
            Route::get('sitemap', [\App\Http\Controllers\Admin\SitemapController::class, 'status']);
            Route::post('sitemap/generate', [\App\Http\Controllers\Admin\SitemapController::class, 'generate']);

            // Location CMS
            Route::get('seo-pages', [LocationCmsController::class, 'index']);
            Route::get('seo-pages/{id}', [LocationCmsController::class, 'show']);
            Route::put('seo-pages/{id}', [LocationCmsController::class, 'update']);
            Route::delete('seo-pages/{id}', [LocationCmsController::class, 'destroy']);
            Route::post('seo-pages/generate-ai', [LocationCmsController::class, 'generateAiContent']);
            Route::post('seo-pages/bulk-import', [LocationCmsController::class, 'bulkImport']);
            Route::post('seo-pages/bulk-action', [LocationCmsController::class, 'bulkAction']);
        });
    });
});
