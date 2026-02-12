<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Savings;

use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'role' => $this->role,
            'joined_at' => optional($this->joined_at)->toISOString(),
        ];
    }
}
