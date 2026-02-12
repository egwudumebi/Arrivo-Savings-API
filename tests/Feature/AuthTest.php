<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function createLoginUser(): User
    {
        return User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 'user',
        ]);
    }

    public function test_can_register_and_receive_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'StrongPassw0rd!',
            'password_confirmation' => 'StrongPassw0rd!',
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
                'user' => ['id', 'name', 'email', 'role', 'created_at'],
            ]);
    }

    public function test_can_login_and_access_me_endpoint(): void
    {
        $this->createLoginUser();

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ])->assertStatus(200);

        $token = $login->json('access_token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/auth/me')
            ->assertStatus(200)
            ->assertJsonPath('email', 'test@example.com');
    }

    public function test_logout_invalidates_token(): void
    {
        $this->createLoginUser();

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ])->assertStatus(200);

        $token = $login->json('access_token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/auth/logout')
            ->assertStatus(204);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/auth/me')
            ->assertStatus(401);
    }

    public function test_refresh_returns_new_token(): void
    {
        $this->createLoginUser();

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ])->assertStatus(200);

        $token = (string) $login->json('access_token');

        $refresh = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/auth/refresh')
            ->assertStatus(200);

        $newToken = (string) $refresh->json('access_token');
        $this->assertNotSame('', $newToken);
    }

    public function test_suspended_user_cannot_login(): void
    {
        $user = $this->createLoginUser();
        $user->suspended_at = now();
        $user->save();

        $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ])->assertStatus(403);
    }
}
