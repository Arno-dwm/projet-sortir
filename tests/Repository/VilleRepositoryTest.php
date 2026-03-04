<?php

namespace App\Tests\Repository;

use App\Entity\Ville;
use App\Repository\VilleRepository;
use PHPUnit\Framework\TestCase;

class VilleRepositoryTest extends TestCase
{
    public function test_get_nom_from_mock_by_code(): void
    {
        $ville = new Ville();
        $ville->setNom("Cousin");
        $ville->setCodePostal("74163");

        $mock = $this->createMock(VilleRepository::class);
        $mock->expects($this->once())
            ->method("findOneBy")
            ->with(['codePostal' => "74163"])
            ->willReturn( $ville);

        $villeAttendu = $mock->findOneBy(['codePostal' => "74163"]);

        $this->assertEquals("Cousin", $villeAttendu->getNom());
    }
}
