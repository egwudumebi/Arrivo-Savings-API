<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserRoleHelpersTest extends TestCase
{
    #[Test]
    public function it_checks_roles_correctly(): void
    {
        $user = new User();
        $user->role = 'user';

        $this->assertTrue($user->hasRole('user'));
        $this->assertFalse($user->hasRole('admin'));
        $this->assertTrue($user->hasAnyRole(['admin', 'user']));
        $this->assertFalse($user->hasAnyRole(['admin']));
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isSuperAdmin());

        $admin = new User();
        $admin->role = 'admin';
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isSuperAdmin());

        $super = new User();
        $super->role = 'super_admin';
        $this->assertTrue($super->isAdmin());
        $this->assertTrue($super->isSuperAdmin());
    }
}
