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
        Route::post('set-role', [AuthController::class, 'setRole']);
        Route::post('update-fcm-token', [AuthController::class, 'updateFcmToken']);
        
        // Chat
        Route::get('conversations', [MessageController::class, 'index']);
        Route::get('messages/{userId}', [MessageController::class, 'show']);
        Route::post('messages', [MessageController::class, 'store']);
        
        // Onboarding / Profile updates that skip the strict 'paid' middleware found in web.php
        Route::post('creator/profile', [\App\Http\Controllers\Creator\CreatorProfileController::class, 'update']);
        Route::post('brand/profile', [\App\Http\Controllers\Brand\BrandProfileController::class, 'update']);
    });
});
