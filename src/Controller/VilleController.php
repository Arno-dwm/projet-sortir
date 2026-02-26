<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ville', name: 'app_ville')]
final class VilleController extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     */
    #[Route('/delete/{id}', name: '_delete', requirements: ['id' => '\d+'])]
    public function create(EntityManagerInterface $em, Request $request, VilleRepository $villeRepo, int $id, SortieRepository $sortieRepo): Response
    {
        $ville = $villeRepo->find($id);
        $sorties = $sortieRepo->findAll();

        $deleted = 0;
        foreach ($sorties as $sortie) {
            if ($sortie->getLieu()->getVille() === $ville) {
                $deleted += 1;
            }
        }

        if ($deleted === 0) {
            $token = $request->query->get('_token');
            if ($this->isCsrfTokenValid('delete_ville' . $ville->getId(), $token)) {

                $em->remove($ville);
                $em->flush();

                $this->addFlash('success', 'La ville de ' . $ville->getNom() . ' a bien été supprimée !');

                return $this->redirectToRoute('app_admin_villes');
            };

        }

        $this->addFlash('danger', "Action impossible");
        return $this->redirectToRoute('app_home');
    }
}
