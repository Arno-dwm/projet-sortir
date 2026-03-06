<?php

namespace App\Controller;

use App\DTO\SortieFilterDTO;
use App\Entity\Sortie;
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

        //$sorties récupère le resultat
        //$qb récupère la requete
        $qb = $form->isSubmitted() && $form->isValid()
            ? $sortieRepository->findByFilters($dto, $user)
            : $sortieRepository->findAllNotCanceledPagin($user);

        //Pour pagination
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 12;
        /*
         * Compter le total SANS limite
         */
        $countQb = clone $qb;
        $totalItems = count(new Paginator($countQb));

        $totalPages = max(1, ceil($totalItems / $limit));

        /*
         * Sécuriser la page en cours
         */
        $page = min($page, $totalPages);

        /*
         * Appliquer la pagination
         */
        $qb->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);



        if($user){
            $results = $inscriptionRepository->findSortieIdsByUser($user);
            $inscriptions = array_column($results, 'sortieId');
        }

        $paginator = new Paginator($qb);

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
