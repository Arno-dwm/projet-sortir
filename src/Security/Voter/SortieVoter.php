<?php

namespace App\Security\Voter;

use App\Repository\SortieRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class SortieVoter extends Voter
{
    public const EDIT = 'SORTIE_EDIT';
    public const VIEW = 'SORTIE_VIEW';
    public const CANCEL = 'SORTIE_CANCEL';

    // constructeur ici pour pouvoir vérifier le rôle d'un utilisateur
    public function __construct(
        private Security $security
    ) {
    }


    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::CANCEL])
            && $subject instanceof \App\Entity\Sortie;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();


        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        $sortie = $subject;

        switch ($attribute) {
            case self::EDIT:
               return $sortie->getOrganisateur() === $user;

            case self::CANCEL:
                return $sortie->getOrganisateur() === $user || $this->security->isGranted('ROLE_ADMIN');
                // moins efficace à terme mais on aurait pu faire || in_array('ROLE_ADMIN', $user->getRoles())
            case self::VIEW:

                return true;
        }

        return false;
    }
}
