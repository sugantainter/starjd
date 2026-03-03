<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AppConfigController extends Controller
{
    /**
     * Provide configuration parameters for the mobile app (e.g. login endpoints and URLs)
     */
    public function config(): JsonResponse
    {
        $baseUrl = url('/'); // Returns https://www.starjd.com/ in production

        return response()->json([
            'environment' => app()->environment(),
            'base_url' => $baseUrl,
            'api_url' => $baseUrl . '/api',
            'endpoints' => [
                'login' => $baseUrl . '/api/login',
                'register_customer' => $baseUrl . '/api/register/customer',
                'forgot_password' => $baseUrl . '/api/forgot-password',
                'logout' => $baseUrl . '/api/logout',
                'me' => $baseUrl . '/api/me',
                'social' => [
                    'google' => $baseUrl . '/auth/google/redirect',
                    'facebook' => $baseUrl . '/auth/facebook/redirect',
                ],
            ]
        ]);
    }
}
