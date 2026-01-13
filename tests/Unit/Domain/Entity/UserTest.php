<?php

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\User;
use App\Domain\Exception\UserBlockedException;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testCreateUser(): void
    {
        $user = User::create('test@example.com', 'hashed_password');

        $this->assertEquals('test@example.com', $user->getUserIdentifier());
        $this->assertEquals('hashed_password', $user->getPassword());
        $this->assertFalse($user->isBlocked());
        $this->assertEquals(0, $user->getFailedLoginAttempts());
    }

    public function testRecordFailedLoginAttempts(): void
    {
        $user = User::create('test@example.com', 'password');

        $user->recordFailedLoginAttempts();
        $this->assertEquals(1, $user->getFailedLoginAttempts());
        $this->assertFalse($user->isBlocked());

        $user->recordFailedLoginAttempts();
        $this->assertEquals(2, $user->getFailedLoginAttempts());
    }

    public function testBlockAfterMaxAttempts(): void
    {
        $user = User::create('test@example.com', 'password');

        // Fail 4 times
        for ($i = 0; $i < 4; $i++) {
            $user->recordFailedLoginAttempts();
        }
        $this->assertEquals(4, $user->getFailedLoginAttempts());
        $this->assertFalse($user->isBlocked());

        // 5th failure should block
        $user->recordFailedLoginAttempts();
        $this->assertEquals(5, $user->getFailedLoginAttempts());
        $this->assertTrue($user->isBlocked());
    }

    public function testExceptionWhenBlocked(): void
    {
        $user = User::create('test@example.com', 'password');

        // Block the user
        for ($i = 0; $i < 5; $i++) {
            $user->recordFailedLoginAttempts();
        }
        $this->assertTrue($user->isBlocked());

        // Next attempt should throw exception
        $this->expectException(UserBlockedException::class);
        $user->recordFailedLoginAttempts();
    }

    public function testUnblock(): void
    {
        $user = User::create('test@example.com', 'password');

        // Block
        for ($i = 0; $i < 5; $i++) {
            $user->recordFailedLoginAttempts();
        }
        $this->assertTrue($user->isBlocked());

        // Unblock
        $user->unblock();
        $this->assertFalse($user->isBlocked());
        $this->assertEquals(0, $user->getFailedLoginAttempts());
    }

    public function testCreateInvalidEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        User::create('invalid-email', 'password');
    }
}

