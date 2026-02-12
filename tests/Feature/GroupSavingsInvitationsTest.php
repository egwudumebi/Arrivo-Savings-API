<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupSavingsInvitationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_creator_can_invite_user_and_user_can_accept_and_be_listed_as_member(): void
    {
        $creator = User::factory()->create();
        $invitee = User::factory()->create();
        $randomUser = User::factory()->create();

        $group = $this->actingAs($creator, 'api')
            ->postJson('/api/v1/group-savings', [
                'name' => 'Trip Fund',
                'currency' => 'NGN',
            ])
            ->assertStatus(201);

        $groupId = (int) $group->json('id');

        $this->actingAs($invitee, 'api')
            ->getJson('/api/v1/group-savings/'.$groupId)
            ->assertStatus(403);

        $this->actingAs($randomUser, 'api')
            ->postJson('/api/v1/group-savings/'.$groupId.'/invite', [
                'invitee_ids' => [$invitee->id],
                'expires_in_hours' => 24,
            ])
            ->assertStatus(403);

        $invite = $this->actingAs($creator, 'api')
            ->postJson('/api/v1/group-savings/'.$groupId.'/invite', [
                'invitee_ids' => [$invitee->id],
                'expires_in_hours' => 24,
            ])
            ->assertStatus(201);

        $invitePayload = $invite->json();
        $inviteList = is_array($invitePayload) && array_key_exists('data', $invitePayload) && is_array($invitePayload['data'])
            ? $invitePayload['data']
            : $invitePayload;

        $this->assertIsArray($inviteList);
        $this->assertNotEmpty($inviteList);

        $listInvites = $this->actingAs($invitee, 'api')
            ->getJson('/api/v1/invitations')
            ->assertStatus(200)
            ->assertJsonPath('0.status', 'pending');

        $listPayload = $listInvites->json();
        $first = is_array($listPayload) && array_key_exists('data', $listPayload) && is_array($listPayload['data'])
            ? ($listPayload['data'][0] ?? null)
            : ($listPayload[0] ?? null);

        $this->assertIsArray($first);
        $invitationId = (int) ($first['id'] ?? 0);
        $this->assertGreaterThan(0, $invitationId);

        $this->actingAs($invitee, 'api')
            ->postJson('/api/v1/invitations/'.$invitationId.'/accept')
            ->assertStatus(201)
            ->assertJsonPath('user.email', $invitee->email);

        $this->assertDatabaseHas('invitations', [
            'id' => $invitationId,
            'status' => 'accepted',
        ]);

        $this->actingAs($invitee, 'api')
            ->getJson('/api/v1/group-savings/'.$groupId.'/members')
            ->assertStatus(200)
            ->assertJsonFragment(['email' => $invitee->email]);

        $this->actingAs($invitee, 'api')
            ->getJson('/api/v1/group-savings/'.$groupId)
            ->assertStatus(200)
            ->assertJsonPath('id', $groupId);

        $inv = Invitation::query()->findOrFail($invitationId);
        $this->assertSame('accepted', $inv->status);
    }
}
