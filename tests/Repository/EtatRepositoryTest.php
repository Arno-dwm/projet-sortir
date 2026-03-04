<?php

namespace Repository;

use App\Entity\Etat;
use App\Repository\EtatRepository;
use PHPUnit\Framework\TestCase;

class EtatRepositoryTest extends TestCase
{
    public function test_get_libelle_from_mock_by_code(): void
    {
        $etat = new Etat();
        $etat->setCode("EC");
        $etat->setLibelle("En Cours");

        $mock = $this->createMock(EtatRepository::class);
        $mock->expects($this->once())
            ->method("findOneBy")
            ->with(['code' => "EC"])
            ->willReturn($etat);

        $etatAttendu = $mock->findOneBy(['code' => "EC"]);

        $this->assertEquals("En Cours", $etatAttendu->getLibelle());
    }
}
