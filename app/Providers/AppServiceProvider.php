<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\RateLimiter;

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
        JsonResource::withoutWrapping();

        RateLimiter::for('auth-login', function (Request $request): Limit {
            if (app()->environment('testing')) {
                return Limit::none();
            }

            $email = (string) $request->input('email', '');

            return Limit::perMinute(5)->by($request->ip().'|'.$email);
        });

        RateLimiter::for('auth-register', function (Request $request): Limit {
            if (app()->environment('testing')) {
                return Limit::none();
            }

            return Limit::perMinute(3)->by($request->ip());
        });

        RateLimiter::for('auth-refresh', function (Request $request): Limit {
            if (app()->environment('testing')) {
                return Limit::none();
            }

            $userId = optional($request->user('api'))->getKey();

            return Limit::perMinute(10)->by((string) ($userId ?? $request->ip()));
        });

        RateLimiter::for('friends', function (Request $request): Limit {
            if (app()->environment('testing')) {
                return Limit::none();
            }

            $userId = optional($request->user('api'))->getKey();

            return Limit::perMinute(30)->by((string) ($userId ?? $request->ip()));
        });

        RateLimiter::for('invites', function (Request $request): Limit {
            if (app()->environment('testing')) {
                return Limit::none();
            }

            $userId = optional($request->user('api'))->getKey();

            return Limit::perMinute(10)->by((string) ($userId ?? $request->ip()));
        });

        RateLimiter::for('group-invite', function (Request $request): Limit {
            if (app()->environment('testing')) {
                return Limit::none();
            }

            $userId = optional($request->user('api'))->getKey();

            return Limit::perMinute(5)->by((string) ($userId ?? $request->ip()));
        });
    }
}
