<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * Mark the user's email as verified (signed URL from email).
     */
    public function verify(EmailVerificationRequest $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Already verified.', 'verified' => true]);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return response()->json([
            'message' => 'Email verified.',
            'verified' => true,
            'redirect' => url('/'),
        ]);
    }

    /**
     * Resend verification email (for authenticated user or by email in body).
     */
    public function resend(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            if ($user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Already verified.'], 422);
            }
            $user->sendEmailVerificationNotification();
            return response()->json(['message' => 'Verification link sent.']);
        }

        $request->validate(['email' => ['required', 'string', 'email']]);
        $user = \App\Models\User::where('email', $request->email)->first();
        if (! $user || $user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Invalid or already verified.'], 422);
        }
        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent.']);
    }
}
