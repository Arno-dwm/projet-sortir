<?php

namespace Entity;

use App\Entity\Etat;
use PHPUnit\Framework\TestCase;

class EtatTest extends TestCase
{
    public function test_setter_etat_libelle(): void
    {
        $etat = new Etat();
        $etat->setLibelle("test");
        $this->assertEquals("test", $etat->getLibelle());
    }

    public function test_setter_etat_code(): void
    {
        $etat = new Etat();
        $etat->setCode("test");
        $this->assertEquals("test", $etat->getCode());
    }


}
