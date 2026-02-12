<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Invitations;

use App\Application\Invitations\InvitationService;
use App\Application\Savings\GroupSavingsService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Invitations\InvitationResource;
use App\Http\Resources\Api\V1\Savings\GroupMemberResource;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class InvitationsController extends Controller
{
    public function __construct(
        private readonly InvitationService $invitationService,
        private readonly GroupSavingsService $groupSavingsService,
    ) {
    }

    public function index(): JsonResponse
    {
        $user = request()->user('api');
        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $invitations = $this->invitationService->listForUser($user);

        $loaded = collect($invitations)->load('inviter');

        return InvitationResource::collection($loaded)->response();
    }

    public function accept(Invitation $invitation): JsonResponse
    {
        $user = request()->user('api');
        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $member = $this->groupSavingsService->acceptInvite($user, $invitation)->load('user');

        return (new GroupMemberResource($member))->response()->setStatusCode(201);
    }

    public function reject(Invitation $invitation): JsonResponse
    {
        $user = request()->user('api');
        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $updated = $this->groupSavingsService->rejectInvite($user, $invitation)->load('inviter');

        return (new InvitationResource($updated))->response();
    }
}
