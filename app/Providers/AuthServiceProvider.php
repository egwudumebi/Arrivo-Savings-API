<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\GroupSavings;
use App\Models\User;
use App\Models\PersonalSavings;
use App\Policies\GroupSavingsPolicy;
use App\Policies\PersonalSavingsPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        GroupSavings::class => GroupSavingsPolicy::class,
        PersonalSavings::class => PersonalSavingsPolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function (?User $user, string $ability): ?bool {
            if ($user === null) {
                return null;
            }

            return $user->isSuperAdmin() ? true : null;
        });
    }
}
