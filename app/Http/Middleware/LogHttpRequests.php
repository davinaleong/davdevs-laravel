<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogHttpRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        $response = $next($request);

        if ($request->is('_debugbar/*') || $request->is('build/*')) {
            return $response;
        }

        ActivityLog::create([
            'channel' => 'http',
            'level' => $response->getStatusCode() >= 500 ? 'error' : ($response->getStatusCode() >= 400 ? 'warning' : 'info'),
            'message' => "{$request->method()} {$request->path()} → {$response->getStatusCode()}",
            'ip_hash' => hash('sha256', $request->ip().date('Y-m-d')),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'status_code' => $response->getStatusCode(),
            'duration_ms' => (int) round((microtime(true) - $start) * 1000),
        ]);

        return $response;
    }
}
