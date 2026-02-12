<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Application\Auth\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Resources\Api\V1\Auth\AuthTokenResource;
use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    #[OA\Post(
        path: '/v1/auth/register',
        operationId: 'authRegister',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['name', 'email', 'password', 'password_confirmation'], properties: [
            new OA\Property(property: 'name', type: 'string', example: 'Jane Doe'),
            new OA\Property(property: 'email', type: 'string', format: 'email', example: 'jane@example.com'),
            new OA\Property(property: 'password', type: 'string', format: 'password', example: 'StrongPassw0rd!'),
            new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'StrongPassw0rd!'),
        ])),
        responses: [
            new OA\Response(response: 201, description: 'Registered', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'access_token', type: 'string'),
                new OA\Property(property: 'token_type', type: 'string'),
                new OA\Property(property: 'expires_in', type: 'integer'),
                new OA\Property(property: 'user', type: 'object'),
            ])),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        $payload = $this->authService->register($request->validated());

        return (new AuthTokenResource($payload))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Post(
        path: '/v1/auth/login',
        operationId: 'authLogin',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['email', 'password'], properties: [
            new OA\Property(property: 'email', type: 'string', format: 'email', example: 'test@example.com'),
            new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
        ])),
        responses: [
            new OA\Response(response: 200, description: 'Authenticated', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'access_token', type: 'string'),
                new OA\Property(property: 'token_type', type: 'string'),
                new OA\Property(property: 'expires_in', type: 'integer'),
                new OA\Property(property: 'user', type: 'object'),
            ])),
            new OA\Response(response: 401, description: 'Invalid credentials'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $payload = $this->authService->login($request->validated());

        if ($payload === null) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        return (new AuthTokenResource($payload))->response();
    }

    #[OA\Post(
        path: '/v1/auth/refresh',
        operationId: 'authRefresh',
        tags: ['Auth'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Token refreshed', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'access_token', type: 'string'),
                new OA\Property(property: 'token_type', type: 'string'),
                new OA\Property(property: 'expires_in', type: 'integer'),
                new OA\Property(property: 'user', type: 'object'),
            ])),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function refresh(): JsonResponse
    {
        $payload = $this->authService->refresh();

        return (new AuthTokenResource($payload))->response();
    }

    #[OA\Post(
        path: '/v1/auth/logout',
        operationId: 'authLogout',
        tags: ['Auth'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 204, description: 'Logged out'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return response()->json(status: 204);
    }

    #[OA\Get(
        path: '/v1/auth/me',
        operationId: 'authMe',
        tags: ['Auth'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Current user', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'id', type: 'integer'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'role', type: 'string', example: 'user'),
                new OA\Property(property: 'created_at', type: 'string', nullable: true),
            ])),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function me(): JsonResponse
    {
        $user = $this->authService->me();

        return (new UserResource($user))->response();
    }
}
