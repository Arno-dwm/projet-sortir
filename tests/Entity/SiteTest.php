<?php

namespace App\Tests\Entity;

use App\Entity\Site;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class SiteTest extends TestCase
{
    public function testSetAndGetNom(): void
    {
        $site= new Site();
        $site->setNom('test');

        $this->assertEquals('test', $site->getNom());

    }
    public function testSetAndGetUser(): void{
        $site= new Site();
        $user= new User();

        $site->addUser($user);

        $this->assertCount(1, $site->getUsers());
        $this->assertTrue($site->getUsers()->contains($user));
        $this->assertEquals($site, $user-> getSite());
    }

    public function testRemoveUser(): void{
        $site= new Site();
        $user= new User();

        $site->addUser($user);
        $site->removeUser($user);


        $this->assertCount(0, $site->getUsers());
        $this->assertFalse($site->getUsers()->contains($user));
        $this->assertNull($user->getSite());
    }
}
