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

    public function test_group_creator_can_invite_user_and_user_can_accept_and_be_listed_as_member(): void
    {
        $creator = User::factory()->create();
        $invitee = User::factory()->create();

        $creatorToken = $this->tokenFor($creator);
        $inviteeToken = $this->tokenFor($invitee);

        $group = $this->withHeader('Authorization', 'Bearer '.$creatorToken)
            ->postJson('/api/v1/group-savings', [
                'name' => 'Trip Fund',
                'currency' => 'NGN',
            ])
            ->assertStatus(201);

        $groupId = (int) $group->json('id');

        $invite = $this->withHeader('Authorization', 'Bearer '.$creatorToken)
            ->postJson('/api/v1/group-savings/'.$groupId.'/invite', [
                'invitee_ids' => [$invitee->id],
                'expires_in_hours' => 24,
            ])
            ->assertStatus(201);

        $this->assertIsArray($invite->json('data'));

        $listInvites = $this->withHeader('Authorization', 'Bearer '.$inviteeToken)
            ->getJson('/api/v1/invitations')
            ->assertStatus(200)
            ->assertJsonPath('data.0.status', 'pending');

        $invitationId = (int) $listInvites->json('data.0.id');

        $this->withHeader('Authorization', 'Bearer '.$inviteeToken)
            ->postJson('/api/v1/invitations/'.$invitationId.'/accept')
            ->assertStatus(201)
            ->assertJsonPath('user.email', $invitee->email);

        $this->assertDatabaseHas('invitations', [
            'id' => $invitationId,
            'status' => 'accepted',
        ]);

        $this->withHeader('Authorization', 'Bearer '.$inviteeToken)
            ->getJson('/api/v1/group-savings/'.$groupId.'/members')
            ->assertStatus(200)
            ->assertJsonFragment(['email' => $invitee->email]);

        $inv = Invitation::query()->findOrFail($invitationId);
        $this->assertSame('accepted', $inv->status);
    }
}
