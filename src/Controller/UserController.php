<?php

namespace App\Controller;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/mon-profil', name: 'app_user')]
    public function monProfil(Request $request, EntityManagerInterface $em): Response
    {
        $User = $this->getUser();

        $userForm = $this->createForm(UserType::class, $User);
        $userForm->handleRequest($request);

        return $this->render('user/profil.html.twig', [
            'user_form' => $userForm,
        ]);
    }
}
