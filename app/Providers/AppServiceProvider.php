<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, [\App\Listeners\LogAuthActivity::class, 'handleLogin']);
        Event::listen(Logout::class, [\App\Listeners\LogAuthActivity::class, 'handleLogout']);

        Gate::before(function (?User $user, string $ability) {
            if ($user && $user->hasPermission($ability)) {
                return true;
            }

            return null;
        });
    }
}
