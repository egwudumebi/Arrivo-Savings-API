<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\GroupSavings;
use App\Models\GroupSavingsMember;
use App\Models\PersonalSavings;
use App\Models\User;
use App\Policies\GroupSavingsPolicy;
use App\Policies\PersonalSavingsPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoliciesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function personal_savings_policy_allows_only_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $savings = PersonalSavings::query()->create([
            'user_id' => $owner->id,
            'name' => 'Test',
            'currency' => 'NGN',
            'status' => 'active',
        ]);

        $policy = new PersonalSavingsPolicy();

        $this->assertTrue($policy->view($owner, $savings));
        $this->assertTrue($policy->update($owner, $savings));
        $this->assertTrue($policy->delete($owner, $savings));

        $this->assertFalse($policy->view($other, $savings));
        $this->assertFalse($policy->update($other, $savings));
        $this->assertFalse($policy->delete($other, $savings));
    }

    #[Test]
    public function group_savings_policy_allows_members_to_view_but_only_creator_to_manage(): void
    {
        $creator = User::factory()->create();
        $member = User::factory()->create();
        $other = User::factory()->create();

        $group = GroupSavings::query()->create([
            'creator_id' => $creator->id,
            'name' => 'Group',
            'currency' => 'NGN',
            'status' => 'active',
        ]);

        GroupSavingsMember::query()->create([
            'group_savings_id' => $group->id,
            'user_id' => $member->id,
            'role' => 'member',
            'joined_at' => now(),
        ]);

        $policy = new GroupSavingsPolicy();

        $this->assertTrue($policy->view($creator, $group));
        $this->assertTrue($policy->manage($creator, $group));

        $this->assertTrue($policy->view($member, $group));
        $this->assertFalse($policy->manage($member, $group));

        $this->assertFalse($policy->view($other, $group));
        $this->assertFalse($policy->manage($other, $group));
    }
}
