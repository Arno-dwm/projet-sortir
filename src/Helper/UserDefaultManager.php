<?php

namespace App\Helper;

use App\Entity\User;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserDefaultManager
{
    public function __construct(SiteRepository $siteRepo, UserPasswordHasherInterface $userPasswordHasher) {

        $this->siteRepository = $siteRepo;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function createUserDefault() {

        $site = $this->siteRepository->find(1);
        $userDefault = New User();
        $userDefault->setUsername('user_default');
        $userDefault->setRoles(['ROLE_USER']);
        $userDefault->setActif(false);
        $userDefault->setMail('user_default@email.com');
        $userDefault->setNom('utilisateur');
        $userDefault->setPrenom('utilisateur');
        $userDefault->setPassword($this->userPasswordHasher->hashPassword($userDefault, '1234'));
        $userDefault->setSite($site);
        return $userDefault;
    }

}
