<?php

use App\Http\Middleware\CmsIpAllowlist;
use App\Http\Middleware\EnsurePublicApiEnabled;
use App\Http\Middleware\LogHttpRequests;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\TwoFactorMiddleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeaders::class);
        $middleware->append(LogHttpRequests::class);
        $middleware->alias([
            'two_factor' => TwoFactorMiddleware::class,
            'cms.ip' => CmsIpAllowlist::class,
            'ensure_api_enabled' => EnsurePublicApiEnabled::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('sitemap:generate')->daily();
        $schedule->command('logs:prune')->daily();
        $schedule->command('exports:prune')->hourly();
    })
    ->create();
