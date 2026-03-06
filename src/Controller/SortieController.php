<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\AnnulerSortieType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\InscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Map\Map;
use Symfony\UX\Map\Marker;
use Symfony\UX\Map\Point;

#[Route('/sortie', name: 'app_sortie')]
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



            $etatCode = match ($action) {
                'CRE' => 'CRE',
                'OUV' => 'OUV',

            };

            $etat = $em->getRepository(Etat::class)->findOneBy(['code' => $etatCode]);
            $sortie->setEtat($etat);

            $em->persist($sortie);

            // Ajout de l'organisateur comme inscrit si publication directe
            if($etatCode === 'OUV') {

                $dateLimite = $sortie->getDateLimiteInscription();

                if ($dateLimite) {
                    $dateLimite->setTime(23, 59, 59);
                    $sortie->setDateLimiteInscription($dateLimite);
                }
                $inscription = new Inscription();
                $inscription->setSortie($sortie);
                $inscription->setParticipant($user);
                $inscription->setDateInscription(new \DateTime('now'));
                $em->persist($inscription);
            }

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
                'latitude' => $lieu->getLatitude(),
                'longitude' => $lieu->getLongitude(),
            ];
        }

        return $this->render('sortie/edit.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'lieux' => $lieuxArray,
            'sortie' => $sortie,
        ]);
    }


    #[Route('/modifier/{id}', name: '_modifier', requirements: ['id' => '\d+'])]
    public function modifier(Request $request, EntityManagerInterface $em, Sortie $sortie): Response
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('SORTIE_EDIT', $sortie);
        $sortieForm = $this->createForm(SortieType::class, $sortie, [
            'user' => $user
        ]);

        $sortieForm->handleRequest($request);

        $action = $request->request->get('action');
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie->setOrganisateur($user);

            if ($action) {

                $etatCode = match ($action) {
                    'CRE' => 'CRE',
                    'OUV' => 'OUV',

                };

                // Ajout de l'organisateur comme inscrit si publication directe
                if($etatCode === 'OUV') {

                    $dateLimite = $sortie->getDateLimiteInscription();

                    if ($dateLimite) {
                        $dateLimite->setTime(23, 59, 59);
                        $sortie->setDateLimiteInscription($dateLimite);
                    }
                    $inscription = new Inscription();
                    $inscription->setSortie($sortie);
                    $inscription->setParticipant($user);
                    $inscription->setDateInscription(new \DateTime('now'));
                    $em->persist($inscription);
                }
                $etat = $em->getRepository(Etat::class)->findOneBy(['code' => $etatCode]);
                $sortie->setEtat($etat);
            }
            $em->flush();

            $this->addFlash('success', "La sortie {$sortie->getNom()} a été enregistré!");
            return $this->redirectToRoute('app_sortie_detail', ['id' => $sortie->getId()]);
        }

        // Lui indiquer Lieux
        $lieux = $em->getRepository(Lieu::class)->findAll();
        $lieuxArray = [];
        foreach ($lieux as $lieu) {
            $lieuxArray[$lieu->getId()] = [
                'rue' => $lieu->getRue(),
                'codePostal' => $lieu->getVille()?->getCodePostal(),
                'latitude' => $lieu->getLatitude(),
                'longitude' => $lieu->getLongitude(),
            ];
        }


        return $this->render('sortie/edit.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'sortie' => $sortie,
            'lieux' => $lieuxArray,
        ]);
    }

    #[Route('/detail/{id}', name: '_detail', requirements: ['id' => '\d+'])]
    public function detail(Sortie $sortie): Response
    {
        // On transmet juste la sortie au Twig
        return $this->render('sortie/detail-sortie.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/inscription/{id}', name: '_inscription', requirements: ['id' => '\d+'])]
    public function inscrption(Sortie $sortie, EntityManagerInterface $em, Request $request): Response
    {
        $token = $request->query->get('_token');
        if ($this->isCsrfTokenValid('inscription_create' . $sortie->getId(), $token)) {
            if ($sortie->getInscriptions()->count() < $sortie->getNbInscriptionsMax()) {
                /*$date = new \DateTime('now');
                $aujourdhui = $date->format('Y-m-d');*/
                if ($sortie->getEtat()->getCode() === 'OUV' && $sortie->getDateLimiteInscription() > new \DateTime('now')) {
                    $inscription = new Inscription();
                    $inscription->setSortie($sortie);
                    $inscription->setDateInscription(new \DateTime('now'));
                    $inscription->setParticipant($this->getUser());
                    $em->persist($inscription);
                    $em->flush();
                    $this->addFlash('success', "Votre inscription à la sortie {$sortie->getNom()} a été enregistrée");

                    if (($sortie->getNbInscriptionsMax() - $sortie->getInscriptions()->count()) == 1) {
                        $etat = $em->getRepository(Etat::class)->findOneBy(['code' => 'CLO']);
                        $sortie->setEtat($etat);
                        $em->persist($sortie);
                        $em->flush();
                    };

                    return $this->redirectToRoute('app_sortie_detail', ['id' => $sortie->getId()]);
                }
            }

            $this->addFlash('danger', "Votre inscription à la sortie {$sortie->getNom()} n'a pas été prise en compte");
            return $this->redirectToRoute('app_sortie_detail', ['id' => $sortie->getId()]);
        }
        $this->addFlash('danger', 'Cette action est impossible !');
        return $this->redirectToRoute('app_sortie_detail', ['id' => $sortie->getId()]);

    }

    #[Route('/desinscription/{id}', name: '_desinscription', requirements: ['id' => '\d+'])]
    public function desinscrire(Sortie $sortie, EntityManagerInterface $em, InscriptionRepository $insRepo, Request $request): Response
    {
        $inscription = $insRepo->findOneBy(['sortie' => $sortie, 'participant' => $this->getUser()]);
        $token = $request->query->get('_token');
        if ($this->isCsrfTokenValid('inscription_delete' . $sortie->getId(), $token)) {
            $em->remove($inscription);
            $em->flush();
            $this->addFlash('success', "Votre inscription à la sortie {$sortie->getNom()} a été supprimé");

            if ($sortie->getInscriptions()->count() < $sortie->getNbInscriptionsMax()) {
                $etat = $em->getRepository(Etat::class)->findOneBy(['code' => 'OUV']);
                $sortie->setEtat($etat);
                $em->persist($sortie);
                $em->flush();
            };

            return $this->redirectToRoute('app_sortie_detail', ['id' => $sortie->getId()]);

        }
        $this->addFlash('danger', 'Cette action est impossible !');
        return $this->redirectToRoute('app_sortie_detail', ['id' => $sortie->getId()]);
    }

    #[Route('/delete/{id}', name: '_suppression', requirements: ['id' => '\d+'])]
    public function delete(Request $request, Sortie $sortie, EntityManagerInterface $em): Response
    {
        $token = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete' . $sortie->getId(), $token)) {
            $this->addFlash('danger', 'Cette action est impossible !');
            return $this->redirectToRoute('app_sortie_detail', ['id' => $sortie->getId()]);
        }

        // Récupérer les inscriptions
        $inscriptions = $sortie->getInscriptions(); // assuming Sortie has OneToMany $inscriptions

        // Vérifier si d'autres participants sont inscrits
        $otherInscriptions = $inscriptions->filter(function ($inscription) {
            return $inscription->getParticipant() !== $this->getUser();
        });

        if ($otherInscriptions->count() > 0) {
            $this->addFlash('danger', "Impossible de supprimer la sortie : d'autres participants sont inscrits !");
            return $this->redirectToRoute('app_sortie_detail', ['id' => $sortie->getId()]);
        }

        // Supprimer la sortie (et automatiquement ton inscription)
        foreach ($inscriptions as $inscription) {
            $em->remove($inscription);
        }
        $em->remove($sortie);
        $em->flush();

        $this->addFlash('success', "La sortie {$sortie->getNom()} a été supprimée !");
        return $this->redirectToRoute('app_home');
    }

    #[Route('/annuler/{id}', name: '_annuler', requirements: ['id' => '\d+'])]
    #[IsGranted('SORTIE_CANCEL',subject: 'sortie')]
    public function annuler(Request $request, Sortie $sortie, EntityManagerInterface $em, EtatRepository $etatRepository): Response
    {

        if ($sortie->getEtat()->getCode() === 'ANN') {
            $this->addFlash('danger', 'Cette sortie est déjà annulée.');
            return $this->redirectToRoute('app_sortie_detail', [
                'id' => $sortie->getId()
            ]);
        }
        $form= $this->createForm(AnnulerSortieType::class, null, [
            'attr' => [
                'id' => 'annulation-form'
            ]
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            //$infos=$sortie->getInfosSortie();
            //$motif = $form->get('motif')->getData();
           // $sortie->setInfosSortie("Motif : {$motif}. {$infos}");
            $motif = $form->get('motif')->getData();
            $sortie->setMotifAnnulation($motif);

            $sortie->setEtat($etatRepository->findOneBy(['code' => 'ANN']));

            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', "La sortie {$sortie->getNom()} a été annulée !");
            return $this->redirectToRoute('app_sortie_detail', ['id'=>$sortie->getId()]);
        }


        return $this->render('sortie/annuler.html.twig', [
            'id' => $sortie->getId(),
            'sortie' => $sortie,
            'formAnnuler' => $form,
        ]);
    }

}
