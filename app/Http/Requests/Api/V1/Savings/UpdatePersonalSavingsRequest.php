<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Savings;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePersonalSavingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:150'],
            'target_amount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'status' => ['sometimes', 'string', 'in:active,paused,closed'],
        ];
    }
}
