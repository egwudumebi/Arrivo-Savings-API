<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Friends;

use App\Application\Friends\FriendsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Friends\SendFriendRequest;
use App\Http\Resources\Api\V1\Friends\FriendRequestResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class FriendsController extends Controller
{
    public function __construct(private readonly FriendsService $friendsService)
    {
    }

    public function index(): JsonResponse
    {
        $user = request()->user('api');
        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $friends = $this->friendsService->listFriends($user);

        return UserResource::collection($friends)->response();
    }

    public function sendRequest(SendFriendRequest $request): JsonResponse
    {
        $user = $request->user('api');
        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $recipient = User::query()->findOrFail((int) $request->validated('recipient_id'));

        $friendRequest = $this->friendsService->sendRequest($user, $recipient)->load(['sender', 'recipient']);

        return (new FriendRequestResource($friendRequest))
            ->response()
            ->setStatusCode(201);
    }

    public function acceptRequest(FriendRequest $friendRequest): JsonResponse
    {
        $user = request()->user('api');
        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $accepted = $this->friendsService->acceptRequest($user, $friendRequest)->load(['sender', 'recipient']);

        return (new FriendRequestResource($accepted))->response();
    }

    public function remove(User $friend): JsonResponse
    {
        $user = request()->user('api');
        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $this->friendsService->removeFriend($user, $friend);

        return response()->json(status: 204);
    }
}
