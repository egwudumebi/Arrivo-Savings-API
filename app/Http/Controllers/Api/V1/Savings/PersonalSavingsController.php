<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Savings;

use App\Application\Savings\PersonalSavingsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Savings\StorePersonalSavingsRequest;
use App\Http\Requests\Api\V1\Savings\UpdatePersonalSavingsRequest;
use App\Http\Resources\Api\V1\Savings\PersonalSavingsResource;
use App\Models\PersonalSavings;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class PersonalSavingsController extends Controller
{
    public function __construct(private readonly PersonalSavingsService $service)
    {
    }

    public function index(): JsonResponse
    {
        $user = request()->user('api');
        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $perPage = (int) request()->query('per_page', 20);

        $items = PersonalSavings::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->paginate($perPage);

        return PersonalSavingsResource::collection($items)->response();
    }

    public function store(StorePersonalSavingsRequest $request): JsonResponse
    {
        $user = $request->user('api');
        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $created = $this->service->create($user, $request->validated());

        return (new PersonalSavingsResource($created))
            ->response()
            ->setStatusCode(201);
    }

    public function show(PersonalSavings $personalSaving): JsonResponse
    {
        $this->authorize('view', $personalSaving);

        return (new PersonalSavingsResource($personalSaving))->response();
    }

    public function update(UpdatePersonalSavingsRequest $request, PersonalSavings $personalSaving): JsonResponse
    {
        $this->authorize('update', $personalSaving);

        $updated = $this->service->update($personalSaving, $request->validated());

        return (new PersonalSavingsResource($updated))->response();
    }

    public function destroy(PersonalSavings $personalSaving): JsonResponse
    {
        $this->authorize('delete', $personalSaving);

        $this->service->delete($personalSaving);

        return response()->json(status: 204);
    }
}
