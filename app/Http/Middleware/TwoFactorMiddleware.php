<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // If 2FA not set up, force setup
        if (!$user->totp_enabled) {
            if (!$request->routeIs('2fa.*')) {
                return redirect()->route('2fa.setup');
            }
            return $next($request);
        }

        // If 2FA set up but not verified in session
        if (!session('2fa_verified')) {
            if (!$request->routeIs('2fa.*')) {
                return redirect()->route('2fa.challenge');
            }
        }

        return $next($request);
    }
}
