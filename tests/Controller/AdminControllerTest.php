<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function test_gestion_utilisateur_success(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        // 1. On récupère l'admin (soit via les fixtures, soit on le crée ici)
        $userRepository = $container->get(UserRepository::class);
        $admin = $userRepository->findOneBy(['username' => 'admin']);

        // 2. On se connecte avec le VRAI objet (qui a un ID et qui est en BDD)
        $client->loginUser($admin);

        // 3. On lance la requête
        $client->request('GET', '/admin/gestion/1');

        // 4. Assertions
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Gestion des participants');
    }

    public function test_gestion_utilisateur_page_non_trouvée(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        // 1. On récupère l'admin (soit via les fixtures, soit on le crée ici)
        $userRepository = $container->get(UserRepository::class);
        $admin = $userRepository->findOneBy(['username' => 'admin']);

        // 2. On se connecte avec le VRAI objet (qui a un ID et qui est en BDD)
        $client->loginUser($admin);

        // 3. On lance la requête
        $client->request('GET', '/admin/gestion/999999');

        // 4. Assertions
        $this->assertResponseStatusCodeSame(404);

    }

}
