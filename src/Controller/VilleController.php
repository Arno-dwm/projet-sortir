<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VilleController extends AbstractController
{
    #[Route('/ville', name: 'app_ville')]
    public function create(EntityManagerInterface $em, Request $request): Response
    {
       /* $token = $request->query->get('_token');
        if ($this->isCsrfTokenValid('ville' . $user->getUserIdentifier(), $token)) {*/
            $ville = new Ville();
            $fomVille = $this->createForm(VilleType::class, $ville);
            $fomVille->handleRequest($request);

            if ($fomVille->isSubmitted() && $fomVille->isValid()) {
                $em->persist($ville);
                $em->flush($ville);
                $this->addFlash('success', 'La ville de ' . $ville->getNom() . ' a bien été enregistrée !');

                return $this->redirectToRoute('app_admin_gestion_ville');
            }

       /* }*/
        $this->addFlash('danger', "Action impossible");
        return $this->redirectToRoute('app_home');
    }
}
