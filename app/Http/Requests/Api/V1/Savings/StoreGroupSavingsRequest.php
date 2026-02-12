<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Savings;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupSavingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'target_amount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'status' => ['nullable', 'string', 'in:active,paused,closed'],
        ];
    }
}
