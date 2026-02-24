<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
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
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $sortieForm = $this->createForm(SortieType::class, $sortie, [
            'user' => $user
        ]);

        $sortieForm->handleRequest($request);
        $action = $request->request->get('action');

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $sortie->setOrganisateur($user);

            $etatLibelle = match($action) {
                'CRE' => 'En Création',
                'OUV' => 'Inscriptions ouvertes',
                'ANN' => 'Annulée',

            };

            $etat = $em->getRepository(Etat::class)->findOneBy(['libelle' => $etatLibelle]);
            $sortie->setEtat($etat);

            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'Une nouvelle proposition de sortie a été enregistrée !');

            return $this->redirectToRoute('app_sortie_detail', [
                'id' => $sortie->getId()
            ]);
        }

        $lieux = $em->getRepository(Lieu::class)->findAll();
        $lieuxArray = [];

        foreach ($lieux as $lieu) {
            $lieuxArray[$lieu->getId()] = [
                'rue' => $lieu->getRue(),
                'codePostal' => $lieu->getVille()?->getCodePostal(),
            ];
        }

        return $this->render('sortie/edit.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'lieux' => $lieuxArray,
        ]);
    }
}
