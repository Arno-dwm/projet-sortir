<?php

namespace Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_isActive(): void
    {
        $user = new User();
        $user->setActif(true);
        $this->assertTrue($user->isActif());

        $user->setActif(false);
        $this->assertFalse($user->isActif());
    }

}
