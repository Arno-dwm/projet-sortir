<?php

namespace App\Helper;


use Doctrine\ORM\EntityManagerInterface;


class SortieManager
{
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

}
