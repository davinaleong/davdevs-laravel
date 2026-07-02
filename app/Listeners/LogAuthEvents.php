<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class LogAuthEvents
{
    public function handleLogin(Login $event): void
    {
        ActivityLog::create([
            'channel' => 'auth',
            'level' => 'info',
            'message' => "Login success: {$event->user->email}",
            'context' => ['user_id' => $event->user->id],
        ]);
    }

    public function handleLogout(Logout $event): void
    {
        ActivityLog::create([
            'channel' => 'auth',
            'level' => 'info',
            'message' => 'Logout: '.($event->user?->email ?? 'unknown'),
            'context' => ['user_id' => $event->user?->id],
        ]);
    }

    public function handleFailed(Failed $event): void
    {
        ActivityLog::create([
            'channel' => 'auth',
            'level' => 'warning',
            'message' => 'Login failed: '.($event->credentials['email'] ?? 'unknown'),
        ]);
    }
}
