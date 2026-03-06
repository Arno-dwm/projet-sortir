<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LieuController extends AbstractController
{
    #[Route('/lieu/create', name: 'app_lieu_create')]
    public function creerLieu(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        //Si soumission du formulaire "nouveau lieu"
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lieu);
            $entityManager->flush();

            //Utilisation de Turbo Stream pour conserver les infos sur le form création Sortie
            return $this->render('lieu/create.stream.html.twig', [
                'lieu' => $lieu,
            ],new Response('', 200, [
                'Content-Type' => 'text/vnd.turbo-stream.html'
            ]));

        }

        //Appel du formulaire pour affichage dans modal-frame
        return $this->render('parts/_form-lieu.html.twig', [
            'formlieu' => $form,
        ]);
    }

    #[Route('/lieu/{id}/json', name: 'lieu_json', methods: ['GET'])]
    public function lieuJson(Lieu $lieu): JsonResponse
    {
        return $this->json([
            'rue' => $lieu->getRue(),
            'latitude' => $lieu->getLatitude(),
            'longitude' => $lieu->getLongitude(),
            'codePostal' => $lieu->getVille()->getCodePostal(),
        ]);
    }
}
