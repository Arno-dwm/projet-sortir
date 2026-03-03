<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class GroupFictures extends Fixture implements DependentFixtureInterface
{

    /**
     * @inheritDoc
     */
    public function getDependencies(): array
    {
        // TODO: Implement getDependencies() method.
        return [
            EtatFixtures::class,
            VilleFixtures::class,
            SiteFixtures::class,
            UserFixtures::class,
            LieuFixtures::class,
            SortieFixtures::class,
            InscriptionFixtures::class
            ];
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager): void
    {
        // TODO: Implement load() method.
    }
}
