<?php

namespace App\Controller;

use App\DTO\VilleFilterDTO;
use App\Entity\Ville;
use App\Form\VilleFilterType;
use App\Form\VilleType;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin', name: 'app_admin')]
final class AdminController extends AbstractController
{
    #[Route('/gestion', name: '_gestion')]
    public function listerUtilisateur(UserRepository $uRepo): Response
    {
        $users = $uRepo->findAll();

        return $this->render('admin/gestion.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/roleAdmin/{id}', name: '_role_admin', requirements: ['id' => '\d+'])]
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
        return $this->redirectToRoute('app_admin_gestion');
        }

        $this->addFlash('danger', "Action impossible");
        return $this->redirectToRoute('app_home');
    }
    #[Route('/roleUser/{id}', name: '_role_user', requirements: ['id' => '\d+'])]
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
            return $this->redirectToRoute('app_admin_gestion');
        }

        $this->addFlash('danger', "Action impossible");
        return $this->redirectToRoute('app_home');
    }
    #[Route('/actif/{id}', name: '_actif', requirements: ['id' => '\d+'])]
    public function rendreActif(UserRepository $uRepo, int $id, EntityManagerInterface $em, Request $request): Response
    {
        $user = $uRepo->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non reconnu');
        }
        $token = $request->query->get('_token');
        if ($this->isCsrfTokenValid('actif' . $user->getId(), $token)) {

            $user->setActif(true);
            $em->persist($user);
            $em->flush($user);

            $this->addFlash('success', ' L\'utilisateur ' . $user->getUsername() . ' est actif');
            return $this->redirectToRoute('app_admin_gestion');
        }

        $this->addFlash('danger', "Action impossible");
        return $this->redirectToRoute('app_home');
    }
    #[Route('/inactif/{id}', name: '_inactif', requirements: ['id' => '\d+'])]
    public function rendreInactif(UserRepository $uRepo, int $id, EntityManagerInterface $em, Request $request): Response
    {
        $user = $uRepo->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non reconnu');
        }

        $sorties = $user->getSortiesOrganisees();

        foreach ($sorties as $sortie) {
            if ($sortie->getEtat()->getCode() == 'OUV' or $sortie->getEtat()->getCode() == 'CLO' OR $sortie->getEtat()->getCode() == 'EC') {
                $this->addFlash('danger', 'Changement à INACTIF impossible pour ' . $user->getUsername() . ' (MOTIF : une activité est à l\'état '. $sortie->getEtat()->getLibelle() . ')' );
                return $this->redirectToRoute('app_admin_gestion');
            }
        }

        $token = $request->query->get('_token');
        if ($this->isCsrfTokenValid('inactif' . $user->getId(), $token)) {

            $user->setActif(false);
            $em->persist($user);
            $em->flush($user);

            $this->addFlash('success', ' L\'utilisateur ' . $user->getUsername() . ' est inactif');
            return $this->redirectToRoute('app_admin_gestion');
        }

        $this->addFlash('danger', "Action impossible");
        return $this->redirectToRoute('app_home');
    }

    #[Route('/villes', name: '_villes')]
    public function gestionVilles(VilleRepository $villeRepo, Request $request, EntityManagerInterface $em): Response
    {

        $dto= new VilleFilterDTO();
        $form = $this->createForm(VilleFilterType::class, $dto);
        $form->handleRequest($request);

        $villes = $form->isSubmitted() && $form->isValid()
            ? $villeRepo->findByFilters($dto)
            : $villeRepo->findAll();

        $ville = new Ville();
        $fomVille = $this->createForm(VilleType::class, $ville);
        $fomVille->handleRequest($request);

        if ($fomVille->isSubmitted() && $fomVille->isValid()) {
            $em->persist($ville);
            $em->flush($ville);
            $this->addFlash('success', 'La ville de ' . $ville->getNom() . ' a bien été enregistrée !');
            return $this->redirectToRoute('app_admin_villes');
        }

        return $this->render('admin/gestion-villes.html.twig', [
            'villes' => $villes,
            'form' => $form,
            'form_ville' => $fomVille,


        ]);
    }
}
