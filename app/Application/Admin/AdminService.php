<?php

declare(strict_types=1);

namespace App\Application\Admin;

use App\Models\GroupSavings;
use App\Models\PersonalSavings;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AdminService
{
    public function listUsers(int $perPage = 20): LengthAwarePaginator
    {
        return User::query()->orderByDesc('id')->paginate($perPage);
    }

    public function suspendUser(User $target, bool $suspend): User
    {
        return DB::transaction(function () use ($target, $suspend): User {
            $target->suspended_at = $suspend ? now() : null;
            $target->save();

            return $target->refresh();
        });
    }

    public function listAllSavings(int $perPage = 20): array
    {
        return [
            'personal' => PersonalSavings::query()->orderByDesc('id')->paginate($perPage),
            'group' => GroupSavings::query()->orderByDesc('id')->paginate($perPage),
        ];
    }
}
