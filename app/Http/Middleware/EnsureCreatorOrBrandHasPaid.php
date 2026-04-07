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
        if (! $user || $user->hasRole('admin')) {
            return $next($request);
        }

        if (! AccessPayment::hasPaidAccess($user)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Payment required to access dashboard.',
                    'requires_payment' => true,
                ], 402);
            }
            $prefix = $user->role === 'studio_owner' ? 'studio' : str_replace('_', '-', $user->role);
            $path = '/' . $prefix . '/choose-plan';
            return redirect($path);
        }

        return $next($request);
    }
}
