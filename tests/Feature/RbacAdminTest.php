<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_access_admin_endpoints(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user, 'api')
            ->getJson('/api/v1/admin/users')
            ->assertStatus(403);

        $this->actingAs($user, 'api')
            ->getJson('/api/v1/admin/savings')
            ->assertStatus(403);
    }

    public function test_admin_can_list_users_and_suspend_user_but_cannot_suspend_self(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin, 'api')
            ->getJson('/api/v1/admin/users')
            ->assertStatus(200)
            ->assertJsonFragment(['email' => $target->email]);

        $this->actingAs($admin, 'api')
            ->patchJson('/api/v1/admin/users/'.$target->id.'/suspend', [
                'suspend' => true,
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
        ]);

        $this->actingAs($admin, 'api')
            ->patchJson('/api/v1/admin/users/'.$admin->id.'/suspend', [
                'suspend' => true,
            ])
            ->assertStatus(422);
    }

    public function test_suspended_user_cannot_access_protected_routes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin, 'api')
            ->patchJson('/api/v1/admin/users/'.$user->id.'/suspend', [
                'suspend' => true,
            ])
            ->assertStatus(200);

        $this->actingAs($user, 'api')
            ->getJson('/api/v1/profile')
            ->assertStatus(403);
    }

    public function test_only_super_admin_can_access_super_admin_endpoints(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $target = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin, 'api')
            ->getJson('/api/v1/super-admin/stats')
            ->assertStatus(403);

        $this->actingAs($superAdmin, 'api')
            ->getJson('/api/v1/super-admin/stats')
            ->assertStatus(200)
            ->assertJsonStructure(['users_total', 'users_suspended', 'personal_savings_total', 'group_savings_total']);

        $this->actingAs($superAdmin, 'api')
            ->patchJson('/api/v1/super-admin/users/'.$target->id.'/promote-admin')
            ->assertStatus(200)
            ->assertJsonPath('role', 'admin');
    }
}
