<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Invitations;

use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvitationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'status' => $this->status,
            'group_savings_id' => $this->group_savings_id,
            'inviter' => new UserResource($this->whenLoaded('inviter')),
            'expires_at' => optional($this->expires_at)->toISOString(),
            'responded_at' => optional($this->responded_at)->toISOString(),
            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}
