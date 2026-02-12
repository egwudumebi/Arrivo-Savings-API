<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Application\Admin\AdminService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\SuspendUserRequest;
use App\Http\Resources\Api\V1\Admin\AdminUserResource;
use App\Http\Resources\Api\V1\Savings\GroupSavingsResource;
use App\Http\Resources\Api\V1\Savings\PersonalSavingsResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function __construct(private readonly AdminService $adminService)
    {
    }

    public function users(): JsonResponse
    {
        $perPage = (int) request()->query('per_page', 20);

        $users = $this->adminService->listUsers($perPage);

        return AdminUserResource::collection($users)->response();
    }

    public function suspend(SuspendUserRequest $request, User $user): JsonResponse
    {
        $actor = $request->user('api');
        if ($actor instanceof User && (int) $actor->id === (int) $user->id) {
            return response()->json(['message' => 'You cannot suspend yourself.'], 422);
        }

        $updated = $this->adminService->suspendUser($user, (bool) $request->validated('suspend'));

        return (new AdminUserResource($updated))->response();
    }

    public function savings(): JsonResponse
    {
        $perPage = (int) request()->query('per_page', 20);

        $data = $this->adminService->listAllSavings($perPage);

        return response()->json([
            'personal' => PersonalSavingsResource::collection($data['personal']),
            'group' => GroupSavingsResource::collection($data['group']),
        ]);
    }
}
