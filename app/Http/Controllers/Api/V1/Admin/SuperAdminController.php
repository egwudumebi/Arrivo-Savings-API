<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Application\Admin\SuperAdminService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\AdminUserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class SuperAdminController extends Controller
{
    public function __construct(private readonly SuperAdminService $service)
    {
    }

    public function promote(User $user): JsonResponse
    {
        if ($user->role === 'super_admin') {
            return response()->json(['message' => 'User is already a super admin.'], 409);
        }

        $updated = $this->service->promoteToAdmin($user);

        return (new AdminUserResource($updated))->response();
    }

    public function stats(): JsonResponse
    {
        return response()->json($this->service->systemStats());
    }
}
