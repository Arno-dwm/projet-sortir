<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use function Symfony\Component\Clock\now;

class SortieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $criteres= ['code'=>[
            "OUV", "CLO", "ANN","FIN", "EC","ARCH"
        ]];
        $etats = $manager->getRepository(Etat::class)->findby($criteres);
        $lieux = $manager->getRepository(Lieu::class)->findAll();
        $organisateurs = $manager->getRepository(User::class)->findAll();


        for ($i = 0; $i < 30; $i++) {
        $organisateur = $organisateurs[array_rand($organisateurs)];
            $sortie = new Sortie();
            $sortie->setNom($faker->realText(30))
                ->setDateHeureDebut($faker->dateTimeBetween('now', '+30 days'))
                ->setDuree($faker->numberBetween(20, 120))
                ->setDateLimiteInscription($faker->dateTimeBetween($sortie->getDateHeureDebut(), '-1 day'))
                ->setNbInscriptionsMax($faker->numberBetween(1, 30))
                ->setInfosSortie($faker->realText(500))
                ->setEtat($faker->randomElement($etats))
                ->setLieu($faker->randomElement($lieux))
                ->setOrganisateur($organisateur);


            $manager->persist($sortie);
            $inscription = new Inscription();
            $inscription->setSortie($sortie);
            $inscription->setDateInscription($faker->dateTime());
            $inscription->setParticipant($organisateur);
           $manager->persist($inscription);

        }
        $etat= new Etat();
        $etat = $manager->getRepository(Etat::class)->findOneBy(['code'=>
        "CRE"
        ]);
        for ($i = 0; $i < 7; $i++) {
            $organisateur = $organisateurs[array_rand($organisateurs)];
            $sortie = new Sortie();
            $sortie->setNom($faker->realText(30))
                ->setDateHeureDebut($faker->dateTimeBetween('now', '+30 days'))
                ->setDuree($faker->numberBetween(20, 120))
                ->setDateLimiteInscription($faker->dateTimeBetween($sortie->getDateHeureDebut(), '-1 day'))
                ->setNbInscriptionsMax($faker->numberBetween(1, 30))
                ->setInfosSortie($faker->realText(500))
                ->setEtat(($etat))
                ->setLieu($faker->randomElement($lieux))
                ->setOrganisateur($organisateur);

            $manager->persist($sortie);
        }

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
