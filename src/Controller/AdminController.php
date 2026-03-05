<?php

namespace App\Controller;

use App\DTO\SiteFilterDTO;
use App\DTO\VilleFilterDTO;
use App\Entity\Site;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\CsvImportType;
use App\Form\SiteFilterType;
use App\Form\SiteType;
use App\Form\VilleFilterType;
use App\Form\VilleType;
use App\Helper\SortieManager;
use App\Helper\UserDefaultManager;
use App\Helper\UserDeleteManager;
use App\Repository\SiteRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin', name: 'app_admin')]
final class AdminController extends AbstractController
{
    #[Route('/gestion/{page}', name: '_gestion', requirements: ['page' => '\d+'])]
    public function listerUtilisateur(UserRepository $uRepo, int $page = 1): Response
    {
        $limit = 10;
        $page = max($page, 1);
        $offset = ($page - 1) * $limit;

        list($nbTotal, $users) = $uRepo->findUsersOrderByUserName($limit, $offset);

        $listAll = $uRepo->findAll();

        $nbPagesMax = ceil($nbTotal / $limit);


        if ($page > $nbPagesMax) {
            throw $this->createNotFoundException("La page $page n'existe pas.");
        }

        $form = $this->createForm(CsvImportType::class);


        return $this->render('admin/gestion-utilisateur.html.twig', [
            'users' => $users,
            'page' => $page,
            'nb_pages_max' => $nbPagesMax,
            'all_users' => $listAll,
            'form_csv' => $form
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
            return $this->redirectToRoute('app_admin_gestion', ['page' => 1]);
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
            return $this->redirectToRoute('app_admin_gestion', ['page' => 1]);
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
            return $this->redirectToRoute('app_admin_gestion', ['page' => 1]);
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

        // Verifier si une sortie avec l'état Inscrption ouverte ou inscription cloturé existe
        foreach ($sorties as $sortie) {
            if ($sortie->getEtat()->getCode() == 'OUV' or $sortie->getEtat()->getCode() == 'CLO' or $sortie->getEtat()->getCode() == 'EC') {
                $this->addFlash('danger', 'Changement à INACTIF impossible pour ' . $user->getUsername() . ' (MOTIF : une activité est à l\'état ' . $sortie->getEtat()->getLibelle() . ')');
                return $this->redirectToRoute('app_admin_gestion', ['page' => 1]);
            }
        }

        $token = $request->query->get('_token');
        if ($this->isCsrfTokenValid('inactif' . $user->getId(), $token)) {

            $user->setActif(false);
            $em->persist($user);
            $em->flush($user);

            $this->addFlash('success', ' L\'utilisateur ' . $user->getUsername() . ' est inactif');
            return $this->redirectToRoute('app_admin_gestion', ['page' => 1]);
        }

        $this->addFlash('danger', "Action impossible");
        return $this->redirectToRoute('app_home');
    }

    #[Route('/supprimer/{id}', name: '_supprimer', requirements: ['id' => '\d+'])]
    public function supprimerUtilisateur(User                   $user,
                                         EntityManagerInterface $em,
                                         Request                $request,
                                         UserDefaultManager     $userDefaultManager,
                                         UserDeleteManager      $deleteManager,
                                         SortieManager          $sortieManager): Response
    {
        $token = $request->query->get('_token');
        if ($this->isCsrfTokenValid('supprimer' . $user->getId(), $token)) {

            if ($sortieManager->isSortieEnCours($user)) {
                $this->addFlash('danger', 'Impossible de supprimer ' . $user->getUsername()
                    . ' (MOTIF : une sortie est en cours)');
                return $this->redirectToRoute('app_admin_gestion');
            }
            $userDefault = $userDefaultManager->findOrCreateUserDefault();
            $deleteManager->deleteReplaceUser($user, $userDefault);
            $em->flush();

            $this->addFlash('success', ' L\'utilisateur ' . $user->getUsername()
                . ' a été remplacé par l\'utilisateur par défaut puis supprimer');
            return $this->redirectToRoute('app_admin_gestion', ['page' => 1]);
        }

        $this->addFlash('danger', "Action impossible");
        return $this->redirectToRoute('app_home');
    }


    #[Route('/villes/{page}', name: '_villes', requirements: ['id' => '\d+'])]
    public function gestionVilles(VilleRepository $villeRepo, Request $request, EntityManagerInterface $em, int $page = 1): Response
    {
        $limit = 10;
        $page = max($page, 1);
        $offset = ($page - 1) * $limit;


        $dto = new VilleFilterDTO();
        $form = $this->createForm(VilleFilterType::class, $dto);
        $form->handleRequest($request);

        list($nbTotal, $villes) = $form->isSubmitted() && $form->isValid()
            ? $villeRepo->findByFiltersPagination($dto, 100, $offset)
            : $villeRepo->findVilleOrderByNom($limit, $offset);

        $nbPagesMax = ceil($nbTotal / $limit);

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
            'page' => $page,
            'nb_pages_max' => $nbPagesMax,


        ]);
    }

    #[Route('/sites/{id?0}', name: '_sites', requirements: ['id' => '\d+'])]
    public function gestionSites(
        SiteRepository         $siteRepo,
        Request                $request,
        EntityManagerInterface $em,
        Site                   $site = null
    ): Response
    {


        $dto = new SiteFilterDTO();
        $formFilter = $this->createForm(SiteFilterType::class, $dto, [
            'method' => 'GET',
            'csrf_protection' => false
        ]);
        $formFilter->handleRequest($request);

        $sites = ($formFilter->isSubmitted() && $formFilter->isValid())
            ? $siteRepo->findByFilters($dto)
            //: $siteRepo->findAll();
            : $siteRepo->findSiteOrderByNom();

        if (!$site) {
            $site = new Site();
        }

        $formSite = $this->createForm(SiteType::class, $site);
        $formSite->handleRequest($request);

        if ($formSite->isSubmitted() && $formSite->isValid()) {

            // Si c'est un nouveau site
            if ($site->getId() === null) {
                $em->persist($site);
                $message = 'a bien été enregistré';
            } else {
                $message = 'a bien été modifié';
            }

            $em->flush();

            $this->addFlash('success', 'Le site ' . $site->getNom() . ' ' . $message . ' !');

            return $this->redirectToRoute('app_admin_sites');
        }

        return $this->render('admin/gestion-sites.html.twig', [
            'sites' => $sites,
            'form_filter' => $formFilter->createView(),
            'form_site' => $formSite->createView(),
            'edit_mode' => $site->getId() !== null
        ]);
    }


}
