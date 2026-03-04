<?php

namespace App\Controller;


use App\Form\ProfilType;
use App\Helper\FileManager;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Event\TestSuite\Sorted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/profil', name: 'app_user')]
final class UserController extends AbstractController
{
    #[Route('/', name: '_mon_profil')]
    public function monProfil(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em, FileManager $fileManager): Response
    {
        $user = $this->getUser();

        $userForm = $this->createForm(ProfilType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            $currentPassword = $userForm->get('currentPassword')->getData();
            $newPassword = $userForm->get('newPassword')->getData();

            // Vérifier mot de passe actuel
            if (!$userPasswordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
            } else {

                // Changement de mot de passe si nécessaire
                if ($newPassword) {
                    $hashedPassword = $userPasswordHasher->hashPassword($user, $newPassword);
                    $user->setPassword($hashedPassword);

                    $em->flush();
                    $this->addFlash('success', 'Mot de passe mis à jour. Veuillez vous reconnecter.');
                    return $this->redirectToRoute('app_logout'); // déconnexion automatique
                }

                // Upload de la nouvelle image et suppression de l'ancienne
                $file = $userForm->get('imgProfil')->getData();
                if ($file instanceof UploadedFile) {
                    $basicName = $user->getUsername();
                    $oldFile   = $user->getUrlPhoto();          // ancienne image à supprimer

                    $newName = $fileManager->uploadFile(
                        $file,
                        $this->getParameter('img_path'),
                        $basicName,
                        $oldFile
                    );

                    $user->setUrlPhoto($newName);
                }


                $em->flush();
                $this->addFlash('success', 'Profil mis à jour avec succès !');

                return $this->redirectToRoute('app_user_mon_profil');
            }
        }

        return $this->render('user/mon-profil.html.twig', [
            'user_form' => $userForm,
            'user' => $user,
        ]);
    }



    #[Route('/detail/{username}', name: '_detail', requirements: ['slug' => '[a-z0-9\-]+'])]
    public function detailProfil(UserRepository $userRepo, EntityManagerInterface $em, string $username, SortieRepository $sortieRepository): Response
    {
        $user = $userRepo->findOneBy(['username' => $username]);

        if (!$user) {
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        $sorties = $sortieRepository->findBy(['organisateur' => $user->getId()]);

        return $this->render('user/detail-profil.html.twig', [
            'user' => $user,
            'sorties' => $sorties,
        ]);
    }


}
