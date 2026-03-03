<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EtatFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $etatCre = new Etat();
        $etatCre->setCode('CRE');
        $etatCre->setLibelle('En création');
        $manager->persist($etatCre);

        $etatOuv = new Etat();
        $etatOuv->setCode('OUV');
        $etatOuv->setLibelle('Ouverte');
        $manager->persist($etatOuv);

        $etatClo = new Etat();
        $etatClo->setCode('CLO');
        $etatClo->setLibelle('Cloturée');
        $manager->persist($etatClo);

        $etatEC = new Etat();
        $etatEC->setCode('EC');
        $etatEC->setLibelle('Activité en cours');
        $manager->persist($etatEC);

        $etatAnn = new Etat();
        $etatAnn->setCode('ANN');
        $etatAnn->setLibelle('Annulée');
        $manager->persist($etatAnn);

        $etatFin = new Etat();
        $etatFin->setCode('FIN');
        $etatFin->setLibelle('Passée');
        $manager->persist($etatFin);

        $etatArch = new Etat();
        $etatArch->setCode('ARCH');
        $etatArch->setLibelle('Archivée');
        $manager->persist($etatArch);


        $manager->flush();
    }
}
