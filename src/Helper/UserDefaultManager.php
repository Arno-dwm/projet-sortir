<?php

namespace App\Helper;

use App\Entity\Site;
use App\Entity\User;
use App\Repository\SiteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserDefaultManager
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $userPasswordHasher;
    private SiteRepository $siteRepository;
    private UserRepository $userRepository;

    public function __construct(SiteRepository $siteRepo, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em)
    {

        $this->siteRepository = $siteRepo;
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->em = $em;
    }


    public function findOrCreateUserDefault(): User
    {

        $userDefault = $this->userRepository->findOneBy(['username' => 'user_default']);

        if (!$userDefault) {
            $siteDefault = $this->siteRepository->findOneBy(['nom' => 'site_default']);
            if (!$siteDefault) {
                $siteDefault = new Site();
                $siteDefault->setNom('site_default');
                $this->em->persist($siteDefault);
            }

            $userDefault = new User();
            $userDefault->setUsername('user_default');
            $userDefault->setRoles(['ROLE_USER']);
            $userDefault->setActif(false);
            $userDefault->setMail('user_default@email.com');
            $userDefault->setNom('utilisateur');
            $userDefault->setPrenom('utilisateur');
            $userDefault->setPassword($this->userPasswordHasher->hashPassword($userDefault, '1234'));
            $userDefault->setSite($siteDefault);
            $this->em->persist($userDefault);
        }

        return $userDefault;
    }

}
