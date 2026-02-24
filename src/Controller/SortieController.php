<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sortie', name: 'app_sortie')]
#[IsGranted('IS_AUTHENTICATED')]
final class SortieController extends AbstractController
{
    #[Route('/create', name: '_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

           $sortie->setOrganisateur($this->getUser());

            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'Une nouvelle poroposition de sortie à été enregistré!');
            return $this->redirectToRoute('app_sortie_detail', ['id' => $sortie->getId()]);
        }
        return $this->render('sortie/edit.html.twig', [
            'sortieForm' => $sortieForm,
        ]);

    }
    //TODO faire le controller modifier, juste copié collé de create pour avoir la route dans détail
    #[Route('/modifier/{id}', name: '_modifier', requirements:['id' => '\d+'])]
    public function modifier(Request $request, EntityManagerInterface $em, Sortie $sortie): Response
    {

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $sortie->setOrganisateur($this->getUser());

            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'Une nouvelle poroposition de sortie à été enregistré!');
            return $this->redirectToRoute('app_sortie_detail', ['id' => $sortie->getId()]);
        }
        return $this->render('sortie/edit.html.twig', [
            'sortieForm' => $sortieForm,
        ]);

    }
    #[Route('/detail/{id}', name: '_detail', requirements:['id' => '\d+'])]
    public function detail(Sortie $sortie): Response
    {


        return $this->render('sortie/detail-sortie.html.twig', [
            'sortie' => $sortie,
        ]);

    }

}
