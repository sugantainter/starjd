<?php

use App\Http\Controllers\Api\AppConfigController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\StudioPublicController;
use App\Http\Controllers\CreatorPublicController;
use App\Http\Controllers\CreatorOptionsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Stateless - No CSRF required)
|--------------------------------------------------------------------------
| These routes are loaded by the RouteServiceProvider and are
| automatically prefixed with /api and use the "api" middleware group
| which does NOT enforce CSRF tokens.
|
*/

// App config
Route::get('/app/config', [AppConfigController::class, 'config']);

// Auth - Public routes (no CSRF, stateless)
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('auth/{provider}/token', [SocialAuthController::class, 'apiCallback']);
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

// Studios - Public (no auth required)
Route::get('studios', [StudioPublicController::class, 'index']);
Route::get('studios/categories', [StudioPublicController::class, 'categories']);
Route::get('studios/{slugOrId}', [StudioPublicController::class, 'show']);

// Creators - Public (no auth required)
Route::get('creators', [CreatorPublicController::class, 'index']);
Route::get('creators/options/filters', [CreatorOptionsController::class, 'filters']);
Route::get('creators/{slug}', [CreatorPublicController::class, 'show']);
