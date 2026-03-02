<?php

namespace App\DataFixtures;

use App\Entity\Site;
use App\Entity\User;
use App\Repository\SiteRepository;
use App\Repository\VilleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SiteFixtures extends Fixture
{
    public function __construct(VilleRepository $villeRepository) {
        $this->villeRepository = $villeRepository;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $villes = $this->villeRepository->findAll();
        $villeSlice = array_slice($villes, 0, 5);

        for ($i = 0; $i < 7; $i++) {
            $nombreAleatoire = $faker->numberBetween(1, 5);
            $site = new Site();
            $site->setNom($faker->randomElement($villeSlice));

            $manager->persist($site);
        }

        $manager->flush();

    }
}
