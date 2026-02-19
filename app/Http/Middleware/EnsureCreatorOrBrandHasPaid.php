<?php

namespace App\Http\Middleware;

use App\Models\AccessPayment;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCreatorOrBrandHasPaid
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! in_array($user->role, ['creator', 'brand'], true)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
            return redirect('/login');
        }

        if (! AccessPayment::hasPaidAccess($user)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Payment required to access dashboard.',
                    'requires_payment' => true,
                ], 402);
            }
            $path = $user->role === 'creator' ? '/creator/choose-plan' : '/brand/choose-plan';
            return redirect($path);
        }

        return $next($request);
    }
}
