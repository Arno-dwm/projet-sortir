<?php

namespace App\Controller;

use App\DTO\SortieFilterDTO;
use App\Form\SortieFilterType;
use App\Repository\InscriptionRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, SortieRepository $sortieRepository, InscriptionRepository $inscriptionRepository): Response
    {
        $dto = new SortieFilterDTO();
        $inscriptions = [];
        $user = $this->getUser();
        $form = $this->createForm(SortieFilterType::class, $dto);

        $form->handleRequest($request);

        //Pour pagination
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 6;

        //$sorties récupère le resultat
        //$qb récupère la requete
        $qb = $form->isSubmitted() && $form->isValid()
            ? $sortieRepository->findByFilters($dto, $user)
            : $sortieRepository->findAllNotCanceledPagin();


        /*
         * Compter le total SANS limite
         */
        $countQb = clone $qb;
        $totalItems = count(new Paginator($countQb));

        $totalPages = max(1, ceil($totalItems / $limit));

        /*
         * Sécuriser la page
         */
        $page = min($page, $totalPages);

        /*
         * Appliquer la pagination
         */
        $qb->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($qb);

        if($user){
            $results = $inscriptionRepository->findSortieIdsByUser($user);
            $inscriptions = array_column($results, 'sortieId');
        }

        return $this->render('main/home.html.twig', [
            'sorties' => $paginator,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'form' => $form,
            'inscriptions' => $inscriptions,
        ]);
        /* pre pagination
        return $this->render('main/home.html.twig', [
            'sorties' => $sorties,
            'form' => $form,
            'inscriptions' => $inscriptions,
        ]);*/
    }
}
