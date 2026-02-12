<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Auth;

use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthTokenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $this['user'];

        return [
            'access_token' => $this['access_token'],
            'token_type' => $this['token_type'],
            'expires_in' => $this['expires_in'],
            'user' => (new UserResource($user)),
        ];
    }
}
