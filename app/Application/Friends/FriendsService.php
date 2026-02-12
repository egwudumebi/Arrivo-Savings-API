<?php

declare(strict_types=1);

namespace App\Application\Friends;

use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FriendsService
{
    public function sendRequest(User $sender, User $recipient): FriendRequest
    {
        if ($sender->id === $recipient->id) {
            abort(422, 'You cannot send a friend request to yourself.');
        }

        $existing = FriendRequest::query()
            ->where(function ($q) use ($sender, $recipient): void {
                $q->where('sender_id', $sender->id)
                    ->where('recipient_id', $recipient->id);
            })
            ->orWhere(function ($q) use ($sender, $recipient): void {
                $q->where('sender_id', $recipient->id)
                    ->where('recipient_id', $sender->id);
            })
            ->first();

        if ($existing !== null) {
            if ($existing->status === 'accepted') {
                abort(409, 'You are already friends.');
            }

            if ($existing->status === 'pending') {
                if ((int) $existing->sender_id === (int) $recipient->id) {
                    abort(409, 'You have a pending friend request from this user.');
                }

                abort(409, 'Friend request already sent.');
            }

            $existing->delete();
        }

        return FriendRequest::query()->create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'status' => 'pending',
        ]);
    }

    public function acceptRequest(User $recipient, FriendRequest $request): FriendRequest
    {
        if ((int) $request->recipient_id !== (int) $recipient->id) {
            abort(403);
        }

        if ($request->status !== 'pending') {
            abort(409, 'Friend request is not pending.');
        }

        return DB::transaction(function () use ($request): FriendRequest {
            $request->status = 'accepted';
            $request->responded_at = now();
            $request->save();

            return $request->refresh();
        });
    }

    public function removeFriend(User $user, User $friend): void
    {
        if ($user->id === $friend->id) {
            abort(422);
        }

        $friendRequest = FriendRequest::query()
            ->where('status', 'accepted')
            ->where(function ($q) use ($user, $friend): void {
                $q->where(function ($q2) use ($user, $friend): void {
                    $q2->where('sender_id', $user->id)->where('recipient_id', $friend->id);
                })->orWhere(function ($q2) use ($user, $friend): void {
                    $q2->where('sender_id', $friend->id)->where('recipient_id', $user->id);
                });
            })
            ->first();

        if ($friendRequest === null) {
            abort(404, 'Friendship not found.');
        }

        DB::transaction(function () use ($friendRequest): void {
            $friendRequest->delete();
        });
    }

    public function listFriends(User $user): array
    {
        $rows = FriendRequest::query()
            ->where('status', 'accepted')
            ->where(function ($q) use ($user): void {
                $q->where('sender_id', $user->id)->orWhere('recipient_id', $user->id);
            })
            ->get(['sender_id', 'recipient_id']);

        $friendIds = $rows
            ->flatMap(function (FriendRequest $fr) use ($user) {
                return [(int) ($fr->sender_id === $user->id ? $fr->recipient_id : $fr->sender_id)];
            })
            ->unique()
            ->values()
            ->all();

        return User::query()->whereIn('id', $friendIds)->orderBy('name')->get()->all();
    }
}
