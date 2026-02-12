<?php

declare(strict_types=1);

namespace App\Application\Invitations;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InvitationService
{
    public function listForUser(User $user): array
    {
        $this->expirePendingForUser($user);

        return Invitation::query()
            ->where('invitee_id', $user->id)
            ->orderByDesc('id')
            ->get()
            ->all();
    }

    public function expirePendingForUser(User $user): int
    {
        return DB::transaction(function () use ($user): int {
            return Invitation::query()
                ->where('invitee_id', $user->id)
                ->where('status', 'pending')
                ->where('expires_at', '<', now())
                ->update([
                    'status' => 'expired',
                    'responded_at' => now(),
                    'updated_at' => now(),
                ]);
        });
    }
}
