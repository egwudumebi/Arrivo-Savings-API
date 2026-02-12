<?php

declare(strict_types=1);

namespace App\Application\Savings;

use App\Models\PersonalSavings;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PersonalSavingsService
{
    public function create(User $user, array $data): PersonalSavings
    {
        return DB::transaction(function () use ($user, $data): PersonalSavings {
            return PersonalSavings::query()->create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'target_amount' => $data['target_amount'] ?? null,
                'currency' => $data['currency'] ?? 'NGN',
                'status' => $data['status'] ?? 'active',
            ]);
        });
    }

    public function update(PersonalSavings $savings, array $data): PersonalSavings
    {
        return DB::transaction(function () use ($savings, $data): PersonalSavings {
            $savings->fill([
                'name' => $data['name'] ?? $savings->name,
                'target_amount' => array_key_exists('target_amount', $data) ? $data['target_amount'] : $savings->target_amount,
                'currency' => $data['currency'] ?? $savings->currency,
                'status' => $data['status'] ?? $savings->status,
            ]);
            $savings->save();

            return $savings->refresh();
        });
    }

    public function delete(PersonalSavings $savings): void
    {
        DB::transaction(function () use ($savings): void {
            $savings->delete();
        });
    }
}
