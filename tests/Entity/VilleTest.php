<?php

namespace App\Tests\Entity;

use App\Entity\Lieu;
use App\Entity\Ville;
use PHPUnit\Framework\TestCase;

class VilleTest extends TestCase
{


    public function testSetAndGetNom(): void
    {
        $ville=new Ville();
        $ville->setNom('Cousin');

        $this->assertEquals('Cousin', $ville->getNom());
    }

    public function testSetAndGetCodePostal(): void{
        $ville=new Ville();
        $ville->setCodePostal('74163');

        $this->assertEquals('74163', $ville->getCodePostal());
    }

    public function testSetAndGetLieu(): void{
        $ville=new Ville();
        $lieu=new Lieu();

        $ville->addLieux($lieu);

        $this->assertCount(1, $ville->getLieux());
        $this->assertTrue($ville->getLieux()->contains($lieu));
        $this->assertEquals($ville, $lieu->getVille());
    }
}
