<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PersonalSavings;
use App\Models\User;

class PersonalSavingsPolicy
{
    public function view(User $user, PersonalSavings $personalSavings): bool
    {
        return $personalSavings->user_id === $user->id;
    }

    public function update(User $user, PersonalSavings $personalSavings): bool
    {
        return $personalSavings->user_id === $user->id;
    }

    public function delete(User $user, PersonalSavings $personalSavings): bool
    {
        return $personalSavings->user_id === $user->id;
    }
}
