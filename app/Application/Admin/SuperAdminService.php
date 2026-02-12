<?php

declare(strict_types=1);

namespace App\Application\Admin;

use App\Models\GroupSavings;
use App\Models\PersonalSavings;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SuperAdminService
{
    public function promoteToAdmin(User $target): User
    {
        return DB::transaction(function () use ($target): User {
            $target->role = 'admin';
            $target->save();

            return $target->refresh();
        });
    }

    public function systemStats(): array
    {
        return [
            'users_total' => User::query()->count(),
            'users_suspended' => User::query()->whereNotNull('suspended_at')->count(),
            'personal_savings_total' => PersonalSavings::query()->count(),
            'group_savings_total' => GroupSavings::query()->count(),
        ];
    }
}
