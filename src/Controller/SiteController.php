<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\SiteFilterType;
use App\Form\SiteType;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('site', name: 'app_site')]
final class SiteController extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     */
    #[Route('/delete/{id}', name: '_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(EntityManagerInterface $em, Request $request, SiteRepository $siteRepo, int $id, UserRepository $userRepo): Response
    {
        $site = $siteRepo->find($id);
        if (!$site) {
            $this->addFlash('danger', 'Site introuvable');
            return $this->redirectToRoute('app_home');
        }


        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete_site' . $site->getId(), $token)) {
            $this->addFlash('danger', "Token CSRF invalide");
            return $this->redirectToRoute('app_home');
        }


        $countUsers = $userRepo->count(['site' => $site]);
        if ($countUsers > 0) {
            $this->addFlash('danger', "Impossible de supprimer ce site : des utilisateurs y sont rattachés !");
            return $this->redirectToRoute('app_home');
        }

        $em->remove($site);
        $em->flush();

        $this->addFlash('success', 'Le site ' . $site->getNom() . ' a bien été supprimé !');
        return $this->redirectToRoute('app_admin_sites');
    }
    #[Route('/update/{id}', name: '_update', requirements: ['id' => '\d+'])]
    public function update(
        Site $site,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        if (!$site) {
            $this->addFlash('danger', 'Site introuvable');
            return $this->redirectToRoute('app_admin_sites');
        }

        $formSite = $this->createForm(SiteType::class, $site);
        $formSite->handleRequest($request);


        $formFilter = $this->createForm(SiteFilterType::class);
        $formFilter->handleRequest($request);

        if ($formSite->isSubmitted() && $formSite->isValid()) {

            $em->flush();

            $this->addFlash('success', 'Le site ' . $site->getNom() . ' a bien été modifié !');

            return $this->redirectToRoute('app_admin_sites');
        }

        return $this->render('admin/gestion-sites.html.twig', [
            'formSite' => $formSite->createView(),
            'formFilter'=> $formFilter->createView(),
            'site' => $site
        ]);
    }
}

