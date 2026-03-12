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
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Stateless - No CSRF required)
|--------------------------------------------------------------------------
| Automatically prefixed with /api. No session/CSRF middleware.
*/

// App config
Route::get('/app/config', [AppConfigController::class, 'config']);

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
Route::get('pages/{slug}', [PageController::class, 'show']);

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
        Route::post('update-profile', [AuthController::class, 'updateProfile']);
        Route::post('set-role', [AuthController::class, 'setRole']);
        Route::post('update-fcm-token', [AuthController::class, 'updateFcmToken']);
        
        // Chat
        Route::get('conversations', [MessageController::class, 'index']);
        Route::get('messages/{userId}', [MessageController::class, 'show']);
        Route::post('messages', [MessageController::class, 'store']);
        
        // Onboarding / Profile updates
        Route::post('creator/onboarding', [\App\Http\Controllers\Creator\CreatorProfileController::class, 'update']);
        Route::post('brand/onboarding', [\App\Http\Controllers\Brand\BrandProfileController::class, 'update']);

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
            Route::delete('social-accounts/{platform}', [\App\Http\Controllers\Creator\CreatorSocialAccountController::class, 'disconnect']);
        });

        Route::middleware(['brand', 'paid'])->prefix('brand')->group(function () {
            Route::get('dashboard', [\App\Http\Controllers\Brand\BrandController::class, 'dashboard']);
            Route::get('campaigns', [\App\Http\Controllers\Brand\BrandCampaignController::class, 'index']);
            Route::get('campaigns/{campaign}', [\App\Http\Controllers\Brand\BrandCampaignController::class, 'show']);
            Route::post('campaigns', [\App\Http\Controllers\Brand\BrandCampaignController::class, 'store']);
            Route::put('campaigns/{campaign}', [\App\Http\Controllers\Brand\BrandCampaignController::class, 'update']);
            Route::patch('campaign-applications/{campaign_application}', [\App\Http\Controllers\Brand\BrandCampaignApplicationController::class, 'update']);
        });

        Route::middleware(['studio_owner'])->prefix('studio-owner')->group(function () {
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
    });
});
