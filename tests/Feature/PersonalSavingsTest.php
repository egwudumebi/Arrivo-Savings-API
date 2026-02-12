<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\PersonalSavings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonalSavingsTest extends TestCase
{
    use RefreshDatabase;

    private function tokenFor(User $user): string
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertStatus(200);

        $token = $response->json('access_token');
        $this->assertIsString($token);

        return $token;
    }

    public function test_only_owner_can_modify_personal_savings(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $ownerToken = $this->tokenFor($owner);
        $otherToken = $this->tokenFor($other);

        $create = $this->withHeader('Authorization', 'Bearer '.$ownerToken)
            ->postJson('/api/v1/personal-savings', [
                'name' => 'My Savings',
                'target_amount' => 5000,
                'currency' => 'NGN',
            ])
            ->assertStatus(201)
            ->assertJsonPath('name', 'My Savings');

        $id = (int) $create->json('id');

        $this->withHeader('Authorization', 'Bearer '.$otherToken)
            ->putJson('/api/v1/personal-savings/'.$id, [
                'name' => 'Hacked',
            ])
            ->assertStatus(403);

        $this->withHeader('Authorization', 'Bearer '.$otherToken)
            ->deleteJson('/api/v1/personal-savings/'.$id)
            ->assertStatus(403);

        $this->withHeader('Authorization', 'Bearer '.$ownerToken)
            ->putJson('/api/v1/personal-savings/'.$id, [
                'name' => 'Updated Name',
            ])
            ->assertStatus(200)
            ->assertJsonPath('name', 'Updated Name');

        $this->assertDatabaseHas('personal_savings', [
            'id' => $id,
            'user_id' => $owner->id,
            'name' => 'Updated Name',
        ]);

        $this->withHeader('Authorization', 'Bearer '.$ownerToken)
            ->deleteJson('/api/v1/personal-savings/'.$id)
            ->assertStatus(204);

        $this->assertDatabaseMissing('personal_savings', [
            'id' => $id,
        ]);

        $this->assertNull(PersonalSavings::query()->find($id));
    }
}
