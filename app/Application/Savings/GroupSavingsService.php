<?php

declare(strict_types=1);

namespace App\Application\Savings;

use App\Models\GroupSavings;
use App\Models\GroupSavingsMember;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GroupSavingsService
{
    public function createGroup(User $creator, array $data): GroupSavings
    {
        return DB::transaction(function () use ($creator, $data): GroupSavings {
            $group = GroupSavings::query()->create([
                'creator_id' => $creator->id,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'target_amount' => $data['target_amount'] ?? null,
                'currency' => $data['currency'] ?? 'NGN',
                'status' => $data['status'] ?? 'active',
            ]);

            GroupSavingsMember::query()->create([
                'group_savings_id' => $group->id,
                'user_id' => $creator->id,
                'role' => 'creator',
                'joined_at' => now(),
            ]);

            return $group->refresh();
        });
    }

    public function inviteUsers(User $creator, GroupSavings $group, array $inviteeIds, int $expiresInHours = 168): array
    {
        if ((int) $group->creator_id !== (int) $creator->id) {
            abort(403);
        }

        return DB::transaction(function () use ($creator, $group, $inviteeIds, $expiresInHours): array {
            $created = [];

            foreach ($inviteeIds as $inviteeId) {
                if ((int) $inviteeId === (int) $creator->id) {
                    continue;
                }

                $alreadyMember = GroupSavingsMember::query()
                    ->where('group_savings_id', $group->id)
                    ->where('user_id', $inviteeId)
                    ->exists();

                if ($alreadyMember) {
                    continue;
                }

                $invitation = Invitation::query()
                    ->where('group_savings_id', $group->id)
                    ->where('invitee_id', $inviteeId)
                    ->whereIn('status', ['pending'])
                    ->first();

                if ($invitation !== null) {
                    if ($invitation->expires_at !== null && $invitation->expires_at->isFuture()) {
                        $created[] = $invitation;
                        continue;
                    }

                    $invitation->status = 'expired';
                    $invitation->responded_at = now();
                    $invitation->save();
                }

                $created[] = Invitation::query()->create([
                    'inviter_id' => $creator->id,
                    'invitee_id' => $inviteeId,
                    'group_savings_id' => $group->id,
                    'token' => (string) Str::uuid(),
                    'type' => 'group',
                    'status' => 'pending',
                    'expires_at' => now()->addHours($expiresInHours),
                ]);
            }

            return $created;
        });
    }

    public function acceptInvite(User $user, Invitation $invitation): GroupSavingsMember
    {
        if ($invitation->invitee_id === null || (int) $invitation->invitee_id !== (int) $user->id) {
            abort(403);
        }

        if ($invitation->status !== 'pending') {
            abort(409, 'Invitation is not pending.');
        }

        if ($invitation->expires_at !== null && $invitation->expires_at->isPast()) {
            DB::transaction(function () use ($invitation): void {
                $invitation->status = 'expired';
                $invitation->responded_at = now();
                $invitation->save();
            });

            abort(410, 'Invitation expired.');
        }

        if ($invitation->group_savings_id === null) {
            abort(422, 'Invitation has no group.');
        }

        return DB::transaction(function () use ($user, $invitation): GroupSavingsMember {
            $member = GroupSavingsMember::query()->firstOrCreate(
                [
                    'group_savings_id' => $invitation->group_savings_id,
                    'user_id' => $user->id,
                ],
                [
                    'role' => 'member',
                    'joined_at' => now(),
                ]
            );

            $invitation->status = 'accepted';
            $invitation->responded_at = now();
            $invitation->save();

            return $member->refresh();
        });
    }

    public function rejectInvite(User $user, Invitation $invitation): Invitation
    {
        if ($invitation->invitee_id === null || (int) $invitation->invitee_id !== (int) $user->id) {
            abort(403);
        }

        if ($invitation->status !== 'pending') {
            abort(409, 'Invitation is not pending.');
        }

        if ($invitation->expires_at !== null && $invitation->expires_at->isPast()) {
            return DB::transaction(function () use ($invitation): Invitation {
                $invitation->status = 'expired';
                $invitation->responded_at = now();
                $invitation->save();

                return $invitation->refresh();
            });
        }

        return DB::transaction(function () use ($invitation): Invitation {
            $invitation->status = 'rejected';
            $invitation->responded_at = now();
            $invitation->save();

            return $invitation->refresh();
        });
    }
}
