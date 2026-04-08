<?php

namespace App\Listeners;

use App\Services\ActivityLogService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class LogAuthActivity
{
    /**
     * Handle successful login.
     */
    public function handleLogin(Login $event): void
    {
        // Auth::user() may not be set yet at this point,
        // so we use the event's user directly.
        ActivityLogService::log(
            'login',
            "User [{$event->user->name}] logged in.",
            null,
            [],
            []
        );
    }

    /**
     * Handle logout.
     */
    public function handleLogout(Logout $event): void
    {
        if (! $event->user) return;

        ActivityLogService::log(
            'logout',
            "User [{$event->user->name}] logged out.",
            null,
            [],
            []
        );
    }
}