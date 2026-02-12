<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FriendsTest extends TestCase
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

    public function test_can_send_and_accept_friend_request_and_list_friends(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $senderToken = $this->tokenFor($sender);

        $send = $this->withHeader('Authorization', 'Bearer '.$senderToken)
            ->postJson('/api/v1/friends/requests', [
                'recipient_id' => $recipient->id,
            ])
            ->assertStatus(201)
            ->assertJsonPath('status', 'pending');

        $friendRequestId = (int) $send->json('id');

        $recipientToken = $this->tokenFor($recipient);

        $this->withHeader('Authorization', 'Bearer '.$recipientToken)
            ->postJson('/api/v1/friends/requests/'.$friendRequestId.'/accept')
            ->assertStatus(200)
            ->assertJsonPath('status', 'accepted');

        $this->withHeader('Authorization', 'Bearer '.$senderToken)
            ->getJson('/api/v1/friends')
            ->assertStatus(200)
            ->assertJsonFragment(['email' => $recipient->email]);

        $this->withHeader('Authorization', 'Bearer '.$recipientToken)
            ->getJson('/api/v1/friends')
            ->assertStatus(200)
            ->assertJsonFragment(['email' => $sender->email]);

        $this->assertDatabaseHas('friend_requests', [
            'id' => $friendRequestId,
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'status' => 'accepted',
        ]);

        $friendRequest = FriendRequest::query()->findOrFail($friendRequestId);

        $this->withHeader('Authorization', 'Bearer '.$senderToken)
            ->deleteJson('/api/v1/friends/'.$recipient->id)
            ->assertStatus(204);

        $this->assertDatabaseMissing('friend_requests', [
            'id' => $friendRequest->id,
        ]);
    }
}
