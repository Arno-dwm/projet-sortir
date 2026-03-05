<?php

namespace App\Helper;


use Doctrine\ORM\EntityManagerInterface;


class UserDeleteManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    function isSortieEnCours($user): bool {

        $sorties = $user->getSortiesOrganisees();
        foreach ($sorties as $sortie) {
            if ($sortie->getEtat()->getCode() == 'OUV'
                || $sortie->getEtat()->getCode() == 'CLO'
                || $sortie->getEtat()->getCode() == 'EC') {

                return true;
            }
        }
        return false;
    }
    function deleteReplaceUser($userToDelete, $userDefault):void {

        $sorties = $userToDelete->getSortiesOrganisees();
        foreach ($sorties as $sortie) {
            $sortie->setOrganisateur($userDefault);
            $this->em->persist($sortie);
        }

        $inscriptions = $userToDelete->getInscriptions();
        foreach ($inscriptions as $inscription) {
            $inscription->setParticipant($userDefault);
            $this->em->persist($inscription);
        }

        $this->em->remove($userToDelete);
    }

}
