<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Error',
    type: 'object',
    required: ['message'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
    ]
)]
#[OA\Schema(
    schema: 'ValidationError',
    type: 'object',
    required: ['message', 'errors'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(type: 'array', items: new OA\Items(type: 'string')),
            example: ['email' => ['The email has already been taken.']]
        ),
    ]
)]
#[OA\Schema(
    schema: 'User',
    type: 'object',
    required: ['id', 'name', 'email', 'role'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Jane Doe'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'jane@example.com'),
        new OA\Property(property: 'role', type: 'string', example: 'user', enum: ['user', 'admin', 'super_admin']),
        new OA\Property(property: 'created_at', type: 'string', nullable: true, example: '2026-01-01T10:00:00.000000Z'),
    ]
)]
#[OA\Schema(
    schema: 'AuthToken',
    type: 'object',
    required: ['access_token', 'token_type', 'expires_in', 'user'],
    properties: [
        new OA\Property(property: 'access_token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...'),
        new OA\Property(property: 'token_type', type: 'string', example: 'bearer'),
        new OA\Property(property: 'expires_in', type: 'integer', example: 900),
        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
    ]
)]
#[OA\Schema(
    schema: 'FriendRequest',
    type: 'object',
    required: ['id', 'sender', 'recipient', 'status'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 10),
        new OA\Property(property: 'sender', ref: '#/components/schemas/User'),
        new OA\Property(property: 'recipient', ref: '#/components/schemas/User'),
        new OA\Property(property: 'status', type: 'string', example: 'pending', enum: ['pending', 'accepted', 'rejected']),
        new OA\Property(property: 'responded_at', type: 'string', nullable: true, example: null),
        new OA\Property(property: 'created_at', type: 'string', nullable: true, example: '2026-01-01T10:00:00.000000Z'),
    ]
)]
#[OA\Schema(
    schema: 'PersonalSavings',
    type: 'object',
    required: ['id', 'user_id', 'name', 'balance', 'currency', 'status'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 5),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Emergency Fund'),
        new OA\Property(property: 'balance', type: 'string', example: '0'),
        new OA\Property(property: 'target_amount', type: 'string', nullable: true, example: '5000'),
        new OA\Property(property: 'currency', type: 'string', example: 'NGN'),
        new OA\Property(property: 'status', type: 'string', example: 'active', enum: ['active', 'paused', 'closed']),
        new OA\Property(property: 'created_at', type: 'string', nullable: true, example: '2026-01-01T10:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', nullable: true, example: '2026-01-01T10:00:00.000000Z'),
    ]
)]
#[OA\Schema(
    schema: 'GroupSavings',
    type: 'object',
    required: ['id', 'creator_id', 'name', 'balance', 'currency', 'status'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 3),
        new OA\Property(property: 'creator_id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Trip Fund'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Saving up for a vacation'),
        new OA\Property(property: 'balance', type: 'string', example: '0'),
        new OA\Property(property: 'target_amount', type: 'string', nullable: true, example: '200000'),
        new OA\Property(property: 'currency', type: 'string', example: 'NGN'),
        new OA\Property(property: 'status', type: 'string', example: 'active', enum: ['active', 'paused', 'closed']),
        new OA\Property(property: 'created_at', type: 'string', nullable: true, example: '2026-01-01T10:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', nullable: true, example: '2026-01-01T10:00:00.000000Z'),
    ]
)]
#[OA\Schema(
    schema: 'Invitation',
    type: 'object',
    required: ['id', 'type', 'status'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 22),
        new OA\Property(property: 'type', type: 'string', example: 'group', enum: ['group']),
        new OA\Property(property: 'status', type: 'string', example: 'pending', enum: ['pending', 'accepted', 'rejected', 'expired']),
        new OA\Property(property: 'group_savings_id', type: 'integer', nullable: true, example: 3),
        new OA\Property(property: 'inviter', ref: '#/components/schemas/User'),
        new OA\Property(property: 'expires_at', type: 'string', nullable: true, example: '2026-01-08T10:00:00.000000Z'),
        new OA\Property(property: 'responded_at', type: 'string', nullable: true, example: null),
        new OA\Property(property: 'created_at', type: 'string', nullable: true, example: '2026-01-01T10:00:00.000000Z'),
    ]
)]
#[OA\Schema(
    schema: 'GroupMember',
    type: 'object',
    required: ['id', 'user', 'role'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 7),
        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
        new OA\Property(property: 'role', type: 'string', example: 'member', enum: ['creator', 'member']),
        new OA\Property(property: 'joined_at', type: 'string', nullable: true, example: '2026-01-01T10:00:00.000000Z'),
    ]
)]
#[OA\Schema(
    schema: 'AdminUser',
    type: 'object',
    required: ['id', 'name', 'email', 'role'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Jane Doe'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'jane@example.com'),
        new OA\Property(property: 'role', type: 'string', example: 'admin', enum: ['user', 'admin', 'super_admin']),
        new OA\Property(property: 'suspended_at', type: 'string', nullable: true, example: null),
        new OA\Property(property: 'created_at', type: 'string', nullable: true, example: '2026-01-01T10:00:00.000000Z'),
    ]
)]
#[OA\Schema(
    schema: 'SystemStats',
    type: 'object',
    required: ['users_total', 'users_suspended', 'personal_savings_total', 'group_savings_total'],
    properties: [
        new OA\Property(property: 'users_total', type: 'integer', example: 120),
        new OA\Property(property: 'users_suspended', type: 'integer', example: 3),
        new OA\Property(property: 'personal_savings_total', type: 'integer', example: 55),
        new OA\Property(property: 'group_savings_total', type: 'integer', example: 12),
    ]
)]
#[OA\Schema(
    schema: 'PaginatedPersonalSavings',
    type: 'object',
    properties: [
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/PersonalSavings')),
        new OA\Property(property: 'links', type: 'object'),
        new OA\Property(property: 'meta', type: 'object'),
    ]
)]
#[OA\Schema(
    schema: 'PaginatedGroupSavings',
    type: 'object',
    properties: [
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/GroupSavings')),
        new OA\Property(property: 'links', type: 'object'),
        new OA\Property(property: 'meta', type: 'object'),
    ]
)]
#[OA\Schema(
    schema: 'PaginatedAdminUsers',
    type: 'object',
    properties: [
        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/AdminUser')),
        new OA\Property(property: 'links', type: 'object'),
        new OA\Property(property: 'meta', type: 'object'),
    ]
)]
#[OA\Get(
    path: '/v1/friends',
    operationId: 'friendsList',
    tags: ['Friends'],
    security: [['bearerAuth' => []]],
    responses: [
        new OA\Response(
            response: 200,
            description: 'List friends',
            content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/User'))
        ),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
    ]
)]
#[OA\Post(
    path: '/v1/friends/requests',
    operationId: 'friendsSendRequest',
    tags: ['Friends'],
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['recipient_id'],
            properties: [
                new OA\Property(property: 'recipient_id', type: 'integer', example: 2),
            ],
            example: ['recipient_id' => 2]
        )
    ),
    responses: [
        new OA\Response(response: 201, description: 'Friend request created (pending)', content: new OA\JsonContent(ref: '#/components/schemas/FriendRequest')),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 409, description: 'Conflict (already friends / pending)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
    ]
)]
#[OA\Post(
    path: '/v1/friends/requests/{friendRequest}/accept',
    operationId: 'friendsAcceptRequest',
    tags: ['Friends'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(name: 'friendRequest', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Friend request accepted', content: new OA\JsonContent(ref: '#/components/schemas/FriendRequest')),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 403, description: 'Forbidden (not the recipient)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 409, description: 'Conflict (not pending)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
    ]
)]
#[OA\Delete(
    path: '/v1/friends/{friend}',
    operationId: 'friendsRemove',
    tags: ['Friends'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(name: 'friend', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), description: 'Friend user id'),
    ],
    responses: [
        new OA\Response(response: 204, description: 'Removed friend'),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 404, description: 'Friendship not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
    ]
)]
#[OA\Get(
    path: '/v1/personal-savings',
    operationId: 'personalSavingsList',
    tags: ['Personal Savings'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 20)),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Paginated personal savings', content: new OA\JsonContent(ref: '#/components/schemas/PaginatedPersonalSavings')),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
    ]
)]
#[OA\Post(
    path: '/v1/personal-savings',
    operationId: 'personalSavingsCreate',
    tags: ['Personal Savings'],
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'My Savings'),
                new OA\Property(property: 'target_amount', type: 'number', nullable: true, example: 5000),
                new OA\Property(property: 'currency', type: 'string', example: 'NGN'),
                new OA\Property(property: 'status', type: 'string', example: 'active', enum: ['active', 'paused', 'closed']),
            ],
            example: ['name' => 'My Savings', 'target_amount' => 5000, 'currency' => 'NGN']
        )
    ),
    responses: [
        new OA\Response(response: 201, description: 'Created', content: new OA\JsonContent(ref: '#/components/schemas/PersonalSavings')),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
    ]
)]
#[OA\Get(
    path: '/v1/personal-savings/{personalSaving}',
    operationId: 'personalSavingsShow',
    tags: ['Personal Savings'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(name: 'personalSaving', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'View personal savings', content: new OA\JsonContent(ref: '#/components/schemas/PersonalSavings')),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 403, description: 'Forbidden (ownership restriction)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
    ]
)]
#[OA\Put(
    path: '/v1/personal-savings/{personalSaving}',
    operationId: 'personalSavingsUpdate',
    tags: ['Personal Savings'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(name: 'personalSaving', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Updated Name'),
                new OA\Property(property: 'target_amount', type: 'number', nullable: true, example: 7500),
                new OA\Property(property: 'currency', type: 'string', example: 'NGN'),
                new OA\Property(property: 'status', type: 'string', example: 'active', enum: ['active', 'paused', 'closed']),
            ],
            example: ['name' => 'Updated Name']
        )
    ),
    responses: [
        new OA\Response(response: 200, description: 'Updated', content: new OA\JsonContent(ref: '#/components/schemas/PersonalSavings')),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 403, description: 'Forbidden (ownership restriction)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
    ]
)]
#[OA\Delete(
    path: '/v1/personal-savings/{personalSaving}',
    operationId: 'personalSavingsDelete',
    tags: ['Personal Savings'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(name: 'personalSaving', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 204, description: 'Deleted'),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 403, description: 'Forbidden (ownership restriction)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
    ]
)]
#[OA\Get(
    path: '/v1/group-savings',
    operationId: 'groupSavingsList',
    tags: ['Group Savings'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 20)),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Paginated group savings', content: new OA\JsonContent(ref: '#/components/schemas/PaginatedGroupSavings')),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
    ]
)]
#[OA\Post(
    path: '/v1/group-savings',
    operationId: 'groupSavingsCreate',
    tags: ['Group Savings'],
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Trip Fund'),
                new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Saving for a vacation'),
                new OA\Property(property: 'target_amount', type: 'number', nullable: true, example: 200000),
                new OA\Property(property: 'currency', type: 'string', example: 'NGN'),
                new OA\Property(property: 'status', type: 'string', example: 'active', enum: ['active', 'paused', 'closed']),
            ],
            example: ['name' => 'Trip Fund', 'currency' => 'NGN']
        )
    ),
    responses: [
        new OA\Response(response: 201, description: 'Created', content: new OA\JsonContent(ref: '#/components/schemas/GroupSavings')),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
    ]
)]
#[OA\Get(
    path: '/v1/group-savings/{groupSaving}',
    operationId: 'groupSavingsShow',
    tags: ['Group Savings'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(name: 'groupSaving', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'View group savings (members only)', content: new OA\JsonContent(ref: '#/components/schemas/GroupSavings')),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 403, description: 'Forbidden (not a member)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
    ]
)]
#[OA\Put(
    path: '/v1/group-savings/{groupSaving}',
    operationId: 'groupSavingsUpdate',
    tags: ['Group Savings'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(name: 'groupSaving', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Updated Group Name'),
                new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Updated description'),
                new OA\Property(property: 'target_amount', type: 'number', nullable: true, example: 250000),
                new OA\Property(property: 'currency', type: 'string', example: 'NGN'),
                new OA\Property(property: 'status', type: 'string', example: 'active', enum: ['active', 'paused', 'closed']),
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: 'Updated (creator only)', content: new OA\JsonContent(ref: '#/components/schemas/GroupSavings')),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 403, description: 'Forbidden (creator only)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
    ]
)]
#[OA\Delete(
    path: '/v1/group-savings/{groupSaving}',
    operationId: 'groupSavingsDelete',
    tags: ['Group Savings'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(name: 'groupSaving', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 204, description: 'Deleted (creator only)'),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 403, description: 'Forbidden (creator only)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
    ]
)]
#[OA\Post(
    path: '/v1/group-savings/{groupSaving}/invite',
    operationId: 'groupSavingsInvite',
    tags: ['Group Savings'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(name: 'groupSaving', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['invitee_ids'],
            properties: [
                new OA\Property(property: 'invitee_ids', type: 'array', items: new OA\Items(type: 'integer'), example: [2, 3]),
                new OA\Property(property: 'expires_in_hours', type: 'integer', nullable: true, example: 24),
            ],
            example: ['invitee_ids' => [2], 'expires_in_hours' => 24]
        )
    ),
    responses: [
        new OA\Response(
            response: 201,
            description: 'Invitations created (creator only). Already-invited or already-member users are skipped.',
            content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Invitation'))
        ),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 403, description: 'Forbidden (creator only)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 422, description: 'Validation error', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
    ]
)]
#[OA\Get(
    path: '/v1/group-savings/{groupSaving}/members',
    operationId: 'groupSavingsMembers',
    tags: ['Group Savings'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(name: 'groupSaving', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'List group members',
            content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/GroupMember'))
        ),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 403, description: 'Forbidden (members only)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
    ]
)]
#[OA\Get(
    path: '/v1/invitations',
    operationId: 'invitationsList',
    tags: ['Invitations'],
    security: [['bearerAuth' => []]],
    responses: [
        new OA\Response(
            response: 200,
            description: 'List invitations for the authenticated user. Pending invitations may be auto-expired if past expires_at.',
            content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Invitation'))
        ),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
    ]
)]
#[OA\Post(
    path: '/v1/invitations/{invitation}/accept',
    operationId: 'invitationAccept',
    tags: ['Invitations'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(name: 'invitation', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 201, description: 'Accepted - returns created/ensured membership', content: new OA\JsonContent(ref: '#/components/schemas/GroupMember')),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 403, description: 'Forbidden (not the invitee)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 409, description: 'Conflict (not pending)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 410, description: 'Gone (expired)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 422, description: 'Unprocessable (no group)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
    ]
)]
#[OA\Post(
    path: '/v1/invitations/{invitation}/reject',
    operationId: 'invitationReject',
    tags: ['Invitations'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(name: 'invitation', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Rejected (or expired if past expires_at)', content: new OA\JsonContent(ref: '#/components/schemas/Invitation')),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 403, description: 'Forbidden (not the invitee)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 409, description: 'Conflict (not pending)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
    ]
)]
#[OA\Get(
    path: '/v1/admin/users',
    operationId: 'adminUsersList',
    tags: ['Admin'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 20)),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Admin: list users', content: new OA\JsonContent(ref: '#/components/schemas/PaginatedAdminUsers')),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 403, description: 'Forbidden (admin only)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
    ]
)]
#[OA\Patch(
    path: '/v1/admin/users/{user}/suspend',
    operationId: 'adminUserSuspend',
    tags: ['Admin'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['suspend'],
            properties: [
                new OA\Property(property: 'suspend', type: 'boolean', example: true),
            ],
            example: ['suspend' => true]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: 'Admin: suspend/unsuspend user', content: new OA\JsonContent(ref: '#/components/schemas/AdminUser')),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 403, description: 'Forbidden (admin only)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 422, description: 'Validation error / cannot suspend self', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
    ]
)]
#[OA\Get(
    path: '/v1/admin/savings',
    operationId: 'adminSavingsList',
    tags: ['Admin'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 20)),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Admin: list all personal and group savings (paginated)',
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'personal', ref: '#/components/schemas/PaginatedPersonalSavings'),
                    new OA\Property(property: 'group', ref: '#/components/schemas/PaginatedGroupSavings'),
                ]
            )
        ),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 403, description: 'Forbidden (admin only)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
    ]
)]
#[OA\Patch(
    path: '/v1/super-admin/users/{user}/promote-admin',
    operationId: 'superAdminPromoteToAdmin',
    tags: ['Super Admin'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Super Admin: promote user to admin', content: new OA\JsonContent(ref: '#/components/schemas/AdminUser')),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 403, description: 'Forbidden (super admin only)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 409, description: 'Conflict (already super admin)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
    ]
)]
#[OA\Get(
    path: '/v1/super-admin/stats',
    operationId: 'superAdminStats',
    tags: ['Super Admin'],
    security: [['bearerAuth' => []]],
    responses: [
        new OA\Response(response: 200, description: 'Super Admin: system statistics', content: new OA\JsonContent(ref: '#/components/schemas/SystemStats')),
        new OA\Response(response: 401, description: 'Unauthenticated', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        new OA\Response(response: 403, description: 'Forbidden (super admin only)', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
    ]
)]
class V1Endpoints
{
}
