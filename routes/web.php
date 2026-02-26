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
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\StateController as AdminStateController;
use App\Http\Controllers\Admin\StepController as AdminStepController;
use App\Http\Controllers\Admin\StudioCategoryController as AdminStudioCategoryController;
use App\Http\Controllers\Admin\AmenityController as AdminAmenityController;
use App\Http\Controllers\Admin\StudioController as AdminStudioController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\HeroController as AdminHeroController;
use App\Http\Controllers\Admin\PartnerController as AdminPartnerController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
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
use App\Http\Controllers\Brand\BrandController as BrandDashboardController;
use App\Http\Controllers\Brand\BrandProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\StudioOwner\StudioOwnerBookingController;
use App\Http\Controllers\StudioOwner\StudioOwnerStudioController;
use App\Http\Controllers\StudioOwner\StudioOwnerStudioImageController;
use App\Http\Controllers\StudioOwner\StudioOwnerAvailabilityController;
use App\Http\Controllers\SocialAuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Storage files (when public/storage symlink is missing or broken, e.g. on Windows)
|--------------------------------------------------------------------------
*/
Route::get('/storage/{path}', function (string $path) {
    $path = str_replace(['..', '\\'], '', $path);
    if ($path === '' || ! Storage::disk('public')->exists($path)) {
        abort(404);
    }
    $fullPath = Storage::disk('public')->path($path);
    $mime = Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';
    return response()->file($fullPath, ['Content-Type' => $mime]);
})->where('path', '.*')->name('storage.serve');

/*
|--------------------------------------------------------------------------
| API Routes (SPA)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    // Auth (guest)
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
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
    Route::get('creators', [CreatorPublicController::class, 'index']);
    Route::get('creators/options/filters', [CreatorOptionsController::class, 'filters']);
    Route::get('creators/{slug}', [CreatorPublicController::class, 'show']);
    Route::get('studios', [StudioPublicController::class, 'index']);
    Route::get('studios/categories', [StudioPublicController::class, 'categories']);
    Route::get('studios/{slugOrId}', [StudioPublicController::class, 'show']);
    Route::get('bookings/calculate', [BookingController::class, 'calculate']);
    Route::get('amenities', fn () => response()->json(\App\Models\Amenity::active()->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug', 'icon'])));
    Route::get('services', [ServiceController::class, 'index']);
    Route::get('services/{slug}', [ServiceController::class, 'show']);
    Route::get('pages/{slug}', [ApiPageController::class, 'show']);
    Route::get('states', fn () => response()->json(\App\Models\State::orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug'])));
    Route::get('cities', function () {
        $stateId = request()->query('state_id');
        return response()->json(\App\Models\City::when($stateId, fn ($q) => $q->where('state_id', $stateId))->with('state:id,name,slug')->orderBy('sort_order')->orderBy('name')->get(['id', 'state_id', 'name', 'slug']));
    });

    Route::middleware(['auth:web'])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
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
        Route::post('collaborations/{collaboration}/accept', [CollaborationController::class, 'accept']);
        Route::post('collaborations/{collaboration}/pay', [CollaborationController::class, 'pay']);
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
    });

    Route::middleware(['auth:web', 'verified', 'agency'])->prefix('agency')->group(function () {
        Route::get('dashboard', fn () => response()->json(['message' => 'Agency dashboard', 'user' => request()->user()->only('id', 'name', 'email')]));
    });

    // Studio owner: allow access without email verification (avoid redirect loop; show verify banner in UI if needed)
    Route::middleware(['auth:web', 'studio_owner'])->prefix('studio')->group(function () {
        Route::get('dashboard', fn () => response()->json(['message' => 'Studio dashboard', 'user' => request()->user()->only('id', 'name', 'email')]));
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
        Route::apiResource('states', AdminStateController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('cities', AdminCityController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('pages', AdminPageController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('studio-owners', [AdminStudioController::class, 'studioOwners']);
        Route::get('studios', [AdminStudioController::class, 'index']);
        Route::post('studios', [AdminStudioController::class, 'store']);
        Route::get('studios/{studio}', [AdminStudioController::class, 'show']);
        Route::put('studios/{studio}', [AdminStudioController::class, 'update']);
        Route::apiResource('studio-categories', AdminStudioCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('amenities', AdminAmenityController::class)->only(['index', 'store', 'update', 'destroy']);
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
