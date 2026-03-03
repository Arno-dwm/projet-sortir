<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LieuFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $villes = $manager->getRepository(Ville::class)->findAll();

        for ($i = 0; $i < 30; $i++) {
            $lieu = new Lieu();
            $lieu->setNom($faker->city())
                ->setRue($faker->streetAddress())
                ->setLatitude($faker->latitude(41, 51.5))
                ->setLongitude($faker->longitude(-5.5, 9.5))
                ->setVille($faker->randomElement($villes))
            ;

            $manager->persist($lieu);
        }
        $manager->flush();
    }
}
