<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/profil', name: 'app_user')]
final class UserController extends AbstractController
{
    #[Route('/', name: '_mon_profil')]
    public function monProfil(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $userForm = $this->createForm(RegistrationFormType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $userForm->get('plainPassword')->getData();
            $user->setPassword(true);

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_login');


        };
        return $this->render('user/mon-profil.html.twig', [
            'user_form' => $userForm,
        ]);
    }

    #[Route('/detail/{username}', name: '_detail',requirements:['slug' => '[a-z0-9\-]+'])]
    public function detailProfil(UserRepository $userRepo, EntityManagerInterface $em, string $username): Response
    {
        $user = $userRepo->findOneBy(['username' => $username]);

        if (!$user) {
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        return $this->render('user/detail-profil.html.twig', [
            'user' => $user,
        ]);
    }

}
