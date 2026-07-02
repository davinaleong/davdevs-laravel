<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsurePublicApiEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = Cache::remember('settings', 3600, function () {
            return Setting::all()->mapWithKeys(fn ($s) => [$s->key => $s->getTypedValue()])->all();
        });

        abort_unless((bool) ($settings['api_enabled'] ?? false), 404);

        return $next($request);
    }
}
