<?php

namespace App\DataFixtures;

use App\Entity\Inscription;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class InscriptionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $inscription = new Inscription();
        $participants = $manager->getRepository(User::class)->findAll();
        $participantsFiltres = array_slice($participants, 0, 20);


        $sorties = $manager->getRepository(Sortie::class)->findAll();


        for ($i = 0; $i < 30; $i++) {
            $participant = $faker->randomElement($participantsFiltres);
            $sortie = $faker->randomElement($sorties);

            if($sortie->getEtat()->getCode() != 'CRE'){
                $dateDebut = $sortie->getDateHeureDebut();
                $dateLimite = $sortie->getDateLimiteInscription();

                $dateLimite = $faker->dateTimeBetween(
                    (clone $dateDebut)->modify('-30 days'),
                    (clone $dateDebut)->modify('-1 day')
                );

                $inscription->setDateInscription($faker->dateTimeBetween($dateLimite, $dateDebut))
                    ->setParticipant($participant)
                    ->setSortie($sortie);
                $manager->persist($inscription);
            }




        }
        $manager->flush();
    }
}
