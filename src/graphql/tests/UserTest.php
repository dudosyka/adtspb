<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Application/Entity/User.php';

use GraphQL\Application\Entity\User;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{

    protected User $user;

    public function setUp(): void{
        $this->user = new User([
            "id" => 1
        ]);
    }

    public function testCorrectRightChecker(): void
    {
        $this->assertInstanceOf(
            true,
            $this->user->hasAccess(1)
        );
        $this->assertInstanceOf(
            false,
            $this->user->hasAccess(2)
        );
    }
}