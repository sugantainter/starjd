<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\StepController as AdminStepController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\HeroController as AdminHeroController;
use App\Http\Controllers\Admin\PartnerController as AdminPartnerController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SectionsController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CreatorPublicController;
use App\Http\Controllers\AccessPaymentController;
use App\Http\Controllers\CollaborationController;
use App\Http\Controllers\Creator\CreatorController as CreatorDashboardController;
use App\Http\Controllers\Creator\CreatorProfileController;
use App\Http\Controllers\Creator\CreatorPackageController;
use App\Http\Controllers\Creator\CreatorSocialAccountController;
use App\Http\Controllers\Creator\CreatorImagePostController;
use App\Http\Controllers\Creator\CreatorFeaturedController;
use App\Http\Controllers\CreatorOptionsController;
use App\Http\Controllers\Brand\BrandController as BrandDashboardController;
use App\Http\Controllers\Brand\BrandProfileController;
use App\Http\Controllers\SocialAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (SPA)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('contact', [ContactController::class, 'store']);
    Route::get('sections', [SectionsController::class, 'index']);
    Route::get('posts', [PostController::class, 'index']);
    Route::get('posts/{slug}', [PostController::class, 'show']);
    Route::get('videos', [VideoController::class, 'index']);
    Route::get('creators', [CreatorPublicController::class, 'index']);
    Route::get('creators/options/filters', [CreatorOptionsController::class, 'filters']);
    Route::get('creators/{slug}', [CreatorPublicController::class, 'show']);
    Route::get('services', [ServiceController::class, 'index']);
    Route::get('services/{slug}', [ServiceController::class, 'show']);

    Route::middleware(['auth:web'])->group(function () {
        Route::get('me', fn () => response()->json(request()->user()->only('id', 'name', 'email', 'role')));
        Route::get('payment/plans', [AccessPaymentController::class, 'plans']);
        Route::get('payment/status', [AccessPaymentController::class, 'status']);
        Route::post('payment/pay', [AccessPaymentController::class, 'pay']);
        Route::get('collaborations', [CollaborationController::class, 'index']);
        Route::post('collaborations', [CollaborationController::class, 'store']);
        Route::post('collaborations/{collaboration}/accept', [CollaborationController::class, 'accept']);
        Route::post('collaborations/{collaboration}/pay', [CollaborationController::class, 'pay']);
    });

    Route::middleware(['auth:web', 'creator', 'paid'])->prefix('creator')->group(function () {
        Route::get('dashboard', [CreatorDashboardController::class, 'dashboard']);
        Route::get('profile', [CreatorProfileController::class, 'show']);
        Route::put('profile', [CreatorProfileController::class, 'update']);
        Route::apiResource('packages', CreatorPackageController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('social-accounts', [CreatorSocialAccountController::class, 'index']);
        Route::post('social-accounts/sync', [CreatorSocialAccountController::class, 'sync']);
        Route::delete('social-accounts/{platform}', [CreatorSocialAccountController::class, 'disconnect']);
        Route::get('image-posts', [CreatorImagePostController::class, 'index']);
        Route::post('image-posts', [CreatorImagePostController::class, 'store']);
        Route::delete('image-posts/{creatorImagePost}', [CreatorImagePostController::class, 'destroy']);
        Route::get('featured-plans', [CreatorFeaturedController::class, 'plans']);
        Route::get('featured/status', [CreatorFeaturedController::class, 'status']);
        Route::post('featured/purchase', [CreatorFeaturedController::class, 'purchase']);
    });

    Route::middleware(['auth:web', 'brand', 'paid'])->prefix('brand')->group(function () {
        Route::get('dashboard', [BrandDashboardController::class, 'dashboard']);
        Route::get('profile', [BrandProfileController::class, 'show']);
        Route::put('profile', [BrandProfileController::class, 'update']);
    });

    Route::middleware(['auth:web', 'admin'])->prefix('admin')->group(function () {
        Route::get('user', fn () => response()->json(request()->user()));
        Route::get('dashboard', [DashboardController::class, 'index']);
        Route::apiResource('categories', AdminCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('testimonials', AdminTestimonialController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('faqs', AdminFaqController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('steps', AdminStepController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('contacts', [ContactMessageController::class, 'index']);
        Route::delete('contacts/{id}', [ContactMessageController::class, 'destroy'])->name('admin.contacts.destroy');
        Route::post('posts/upload', [AdminPostController::class, 'uploadImage']);
Route::apiResource('posts', AdminPostController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('videos', AdminVideoController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('hero', [AdminHeroController::class, 'show']);
        Route::put('hero', [AdminHeroController::class, 'update']);
        Route::post('hero/upload', [AdminHeroController::class, 'upload']);
        Route::apiResource('partners', AdminPartnerController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::post('partners/upload', [AdminPartnerController::class, 'upload']);
        Route::apiResource('services', AdminServiceController::class)->only(['index', 'store', 'update', 'destroy']);
    });
});

/*
|--------------------------------------------------------------------------
| Social / OAuth login & registration (must be before SPA catch-all)
|--------------------------------------------------------------------------
*/
Route::get('/auth/google/redirect', [SocialAuthController::class, 'googleRedirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [SocialAuthController::class, 'googleCallback'])->name('auth.google.callback');
Route::get('/auth/facebook/redirect', [SocialAuthController::class, 'facebookRedirect'])->name('auth.facebook.redirect');
Route::get('/auth/facebook/callback', [SocialAuthController::class, 'facebookCallback'])->name('auth.facebook.callback');

/*
|--------------------------------------------------------------------------
| Web Routes (SPA fallback)
|--------------------------------------------------------------------------
| All front-end routes are handled by the Vue SPA, including: /, /about,
| /contact, /privacy, /terms, /blog, /services, /creators, /login, /register,
| /creator/*, /brand/*, /admin/*, etc.
*/
Route::get('/{any?}', [HomeController::class, 'index'])->where('any', '.*')->name('home');
