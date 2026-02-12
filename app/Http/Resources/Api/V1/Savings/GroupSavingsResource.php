<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Savings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupSavingsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'creator_id' => $this->creator_id,
            'name' => $this->name,
            'description' => $this->description,
            'balance' => (string) $this->balance,
            'target_amount' => $this->target_amount !== null ? (string) $this->target_amount : null,
            'currency' => $this->currency,
            'status' => $this->status,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
