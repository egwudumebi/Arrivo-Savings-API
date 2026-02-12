<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Savings;

use Illuminate\Foundation\Http\FormRequest;

class InviteUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invitee_ids' => ['required', 'array', 'min:1'],
            'invitee_ids.*' => ['integer', 'distinct', 'exists:users,id'],
            'expires_in_hours' => ['nullable', 'integer', 'min:1', 'max:720'],
        ];
    }
}
