<?php

namespace App\DataFixtures;

use AllowDynamicProperties;
use App\Entity\User;
use App\Repository\SiteRepository;
use Container4Tr1aC7\getSiteRepositoryService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

#[AllowDynamicProperties]
class UserFixtures extends Fixture
{
    public function __construct(SiteRepository $siteRepository)
    {
        $this->siteRepository = $siteRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $sites = $this->siteRepository->findAll();

        for ($i = 0; $i < 30; $i++) {
            $user = new User();
            $user->setUsername($faker->userName());
            $user->setNom($faker->lastName());
            $user->setPrenom($faker->firstName());
            $user->setMail($faker->email());
            $user->setPassword(password_hash('1234', PASSWORD_DEFAULT));
            $user->setSite($faker->randomElement($sites));
            $user->setRoles(['ROLE_USER']);
            $user->setActif(true);
            $user->setTelephone($faker->phoneNumber());
            $user->setUrlPhoto('photo-profil-defaut.jpg');

            $manager->persist($user);
        }

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setNom($faker->lastName());
        $admin->setPrenom($faker->firstName());
        $admin->setMail($faker->email());
        $admin->setPassword(password_hash('1234', PASSWORD_DEFAULT));
        $admin->setSite($faker->randomElement($sites));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setActif(true);
        $admin->setTelephone($faker->phoneNumber());
        $admin->setUrlPhoto('photo-profil-defaut.jpg');

        $manager->persist($admin);

        $userDefault = new User();
        $userDefault->setUsername('user_default');
        $userDefault->setNom($faker->lastName());
        $userDefault->setPrenom($faker->firstName());
        $userDefault->setMail('user_default@mail.com');
        $userDefault->setPassword(password_hash('1234', PASSWORD_DEFAULT));
        $userDefault->setSite($faker->randomElement($sites));
        $userDefault->setRoles(['ROLE_USER']);
        $userDefault->setActif(false);
        $userDefault->setTelephone('0000000000');
        $userDefault->setUrlPhoto('photo-profil-defaut.jpg');

        $manager->persist($userDefault);

        $manager->flush();

    }
}
