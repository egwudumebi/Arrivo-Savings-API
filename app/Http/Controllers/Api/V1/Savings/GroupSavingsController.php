<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Savings;

use App\Application\Savings\GroupSavingsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Savings\InviteUsersRequest;
use App\Http\Requests\Api\V1\Savings\StoreGroupSavingsRequest;
use App\Http\Requests\Api\V1\Savings\UpdateGroupSavingsRequest;
use App\Http\Resources\Api\V1\Invitations\InvitationResource;
use App\Http\Resources\Api\V1\Savings\GroupMemberResource;
use App\Http\Resources\Api\V1\Savings\GroupSavingsResource;
use App\Models\GroupSavings;
use App\Models\GroupSavingsMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\JsonResponse;

class GroupSavingsController extends Controller
{
    public function __construct(private readonly GroupSavingsService $service)
    {
    }

    public function index(): JsonResponse
    {
        $user = request()->user('api');
        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $perPage = (int) request()->query('per_page', 20);

        $groupIds = GroupSavingsMember::query()
            ->where('user_id', $user->id)
            ->pluck('group_savings_id')
            ->all();

        $items = GroupSavings::query()
            ->where(function ($q) use ($user, $groupIds): void {
                $q->where('creator_id', $user->id);
                if (! empty($groupIds)) {
                    $q->orWhereIn('id', $groupIds);
                }
            })
            ->orderByDesc('id')
            ->paginate($perPage);

        return GroupSavingsResource::collection($items)->response();
    }

    public function store(StoreGroupSavingsRequest $request): JsonResponse
    {
        $user = $request->user('api');
        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $group = $this->service->createGroup($user, $request->validated());

        return (new GroupSavingsResource($group))
            ->response()
            ->setStatusCode(201);
    }

    public function show(GroupSavings $groupSaving): JsonResponse
    {
        $this->authorize('view', $groupSaving);

        return (new GroupSavingsResource($groupSaving))->response();
    }

    public function update(UpdateGroupSavingsRequest $request, GroupSavings $groupSaving): JsonResponse
    {
        $this->authorize('update', $groupSaving);

        $groupSaving->fill($request->validated());
        $groupSaving->save();

        return (new GroupSavingsResource($groupSaving->refresh()))->response();
    }

    public function destroy(GroupSavings $groupSaving): JsonResponse
    {
        $this->authorize('delete', $groupSaving);

        $groupSaving->delete();

        return response()->json(status: 204);
    }

    public function invite(InviteUsersRequest $request, GroupSavings $groupSaving): JsonResponse
    {
        $this->authorize('manage', $groupSaving);

        $user = $request->user('api');
        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $inviteeIds = $request->validated('invitee_ids');
        $expiresInHours = (int) ($request->validated('expires_in_hours') ?? 168);

        $invitations = $this->service->inviteUsers($user, $groupSaving, $inviteeIds, $expiresInHours);

        $loaded = EloquentCollection::make($invitations)->load('inviter');

        return InvitationResource::collection($loaded)->response()->setStatusCode(201);
    }

    public function members(GroupSavings $groupSaving): JsonResponse
    {
        $this->authorize('listMembers', $groupSaving);

        $members = GroupSavingsMember::query()
            ->where('group_savings_id', $groupSaving->id)
            ->with('user')
            ->orderBy('id')
            ->get();

        return GroupMemberResource::collection($members)->response();
    }
}
