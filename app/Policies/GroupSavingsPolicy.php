<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\GroupSavings;
use App\Models\GroupSavingsMember;
use App\Models\User;

class GroupSavingsPolicy
{
    public function view(User $user, GroupSavings $groupSavings): bool
    {
        if ($groupSavings->creator_id === $user->id) {
            return true;
        }

        return GroupSavingsMember::query()
            ->where('group_savings_id', $groupSavings->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    public function update(User $user, GroupSavings $groupSavings): bool
    {
        return $groupSavings->creator_id === $user->id;
    }

    public function delete(User $user, GroupSavings $groupSavings): bool
    {
        return $groupSavings->creator_id === $user->id;
    }

    public function manage(User $user, GroupSavings $groupSavings): bool
    {
        return $groupSavings->creator_id === $user->id;
    }

    public function listMembers(User $user, GroupSavings $groupSavings): bool
    {
        return $this->view($user, $groupSavings);
    }
}
