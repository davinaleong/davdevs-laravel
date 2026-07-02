<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CmsIpAllowlist
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowlist = array_filter(explode(',', (string) config('services.cms_ip_allowlist')));

        if (empty($allowlist)) {
            return $next($request);
        }

        abort_unless(in_array($request->ip(), $allowlist, true), 403, 'Access denied.');

        return $next($request);
    }
}
