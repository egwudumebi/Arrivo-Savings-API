<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Friends;

use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FriendRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sender' => new UserResource($this->whenLoaded('sender')),
            'recipient' => new UserResource($this->whenLoaded('recipient')),
            'status' => $this->status,
            'responded_at' => optional($this->responded_at)->toISOString(),
            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}
