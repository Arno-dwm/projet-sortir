<?php

namespace App\Controller;

use App\DTO\SortieFilterDTO;
use App\Form\SortieFilterType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, SortieRepository $sortieRepository): Response
    {
        $dto = new SortieFilterDTO();
        $user = $this->getUser();
        $form = $this->createForm(SortieFilterType::class, $dto);

        $form->handleRequest($request);

        $sorties = $form->isSubmitted() && $form->isValid()
            ? $sortieRepository->findByFilters($dto, $user)
            : $sortieRepository->findAll();

        return $this->render('main/home.html.twig', [
            'sorties' => $sorties,
            'form' => $form,
        ]);
    }
}
