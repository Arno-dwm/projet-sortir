<?php

namespace App\Tests\Repository;

use App\Entity\Site;
use App\Repository\SiteRepository;
use PHPUnit\Framework\TestCase;

class SiteRepositoryTest extends TestCase
{
    public function test_get_nom_from_mock_by_code(): void
    {
        $site = new Site();
        $site->setNom("Cousin");


        $mock = $this->createMock(SiteRepository::class);
        $mock->expects($this->once())
            ->method("findOneBy")
            ->with(['nom' => "Cousin"])
            ->willReturn($site);

        $siteAttendu = $mock->findOneBy(['nom' => "Cousin"]);

        $this->assertEquals("Cousin", $siteAttendu->getNom());
    }
}
