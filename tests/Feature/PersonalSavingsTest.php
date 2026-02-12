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

    public function test_only_owner_can_modify_personal_savings(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $create = $this->actingAs($owner, 'api')
            ->postJson('/api/v1/personal-savings', [
                'name' => 'My Savings',
                'target_amount' => 5000,
                'currency' => 'NGN',
            ])
            ->assertStatus(201)
            ->assertJsonPath('name', 'My Savings');

        $id = (int) $create->json('id');

        $this->actingAs($other, 'api')
            ->putJson('/api/v1/personal-savings/'.$id, [
                'name' => 'Hacked',
            ])
            ->assertStatus(403);

        $this->actingAs($other, 'api')
            ->deleteJson('/api/v1/personal-savings/'.$id)
            ->assertStatus(403);

        $this->actingAs($owner, 'api')
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

        $this->actingAs($owner, 'api')
            ->deleteJson('/api/v1/personal-savings/'.$id)
            ->assertStatus(204);

        $this->assertDatabaseMissing('personal_savings', [
            'id' => $id,
        ]);

        $this->assertNull(PersonalSavings::query()->find($id));
    }
}
