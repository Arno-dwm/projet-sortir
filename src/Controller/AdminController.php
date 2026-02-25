<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function listerUtilisateur(UserRepository $uRepo): Response
    {
        $users = $uRepo->findAll();

        return $this->render('admin/gestion.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/roleAdmin/{id}', name: 'app_role_admin', requirements: ['id' => '\d+'])]
    public function devenirAdmin(UserRepository $uRepo, int $id, EntityManagerInterface $em, Request $request): Response
    {
        $user = $uRepo->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non reconnu');
        }
        $token = $request->query->get('_token');
        if ($this->isCsrfTokenValid('role_admin' . $user->getId(), $token)) {

        $user->setRoles(['ROLE_ADMIN']);
        $em->persist($user);
        $em->flush($user);

        $this->addFlash('success', 'Le rôle de ' . $user->getUsername() . ' a été modifié en ADMIN');
        return $this->redirectToRoute('app_admin');
        }

        $this->addFlash('danger', "Action impossible");
        return $this->redirectToRoute('app_home');
    }
    #[Route('/roleUser/{id}', name: 'app_role_user', requirements: ['id' => '\d+'])]
    public function enleverAdmin(UserRepository $uRepo, int $id, EntityManagerInterface $em, Request $request): Response
    {
        $user = $uRepo->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non reconnu');
        }
        $token = $request->query->get('_token');
        if ($this->isCsrfTokenValid('role_admin' . $user->getId(), $token)) {

            $user->setRoles(['ROLE_USER']);
            $em->persist($user);
            $em->flush($user);

            $this->addFlash('success', 'Le rôle de ' . $user->getUsername() . ' a été modifié en USER');
            return $this->redirectToRoute('app_admin');
        }

        $this->addFlash('danger', "Action impossible");
        return $this->redirectToRoute('app_home');
    }
}
