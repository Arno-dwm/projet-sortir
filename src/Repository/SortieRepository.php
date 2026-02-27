<?php

namespace App\Repository;

use App\DTO\SortieFilterDTO;
use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }


    public function findByFilters(SortieFilterDTO $filters, User $user)
    {

        $qb = $this->createQueryBuilder('s')
            ->join('s.organisateur', 'o')
            ->join('s.inscriptions', 'i')
            ->leftJoin('s.etat', 'e')
            ->addSelect('e')
            ->addSelect("
                    CASE
                        WHEN e.code = 'CRE' THEN 1
                        WHEN e.code = 'OUV' THEN 2
                        WHEN e.code = 'FIN' THEN 3
                        ELSE 4
                    END AS HIDDEN ordreEtat
                ")
            ->orderBy('ordreEtat', 'ASC')
            ->addOrderBy('s.dateHeureDebut', 'ASC');
        ;

        if (!empty($filters->site)) {
            $qb->andWhere('o.site = :site')
                ->setParameter('site', $filters->site);
        }

        if ($filters->inputSearch) {
            $qb->andWhere('s.nom LIKE :search')
                ->setParameter('search', '%'.$filters->inputSearch.'%');
        }
        if ($filters->isOrganisateur) {
            $qb->andWhere('s.organisateur = :user')
                ->setParameter('user', $user->getId());
        }
        if ($filters->isInscrit) {
            $qb->andWhere('i.participant = :user')
                ->setParameter('user', $user);
        }
        if ($filters->isNotInscrit && $user) {
            $qb->leftJoin('s.inscriptions', 'i2', 'WITH', 'i2.participant = :user')
                ->andWhere('i2.id IS NULL')
                ->setParameter('user', $user);
        }
        if($filters->ended){
            $qb->andWhere('s.etat = :etat')
                ->setParameter('etat', 5);
        }

        // Filtre dates
        if ($filters->dateMin) {
            $qb->andWhere('s.dateHeureDebut >= :dateMin')
                ->setParameter('dateMin', $filters->dateMin);
        }

        if ($filters->dateMax) {
            $qb->andWhere('s.dateHeureDebut <= :dateMax')
                ->setParameter('dateMax', $filters->dateMax);
        }



       // return $qb->getQuery()->getResult();
        return $qb;
    }

    public function findAllNotCanceled(){
        $qb = $this->createQueryBuilder('s');
        $qb  ->leftJoin('s.etat', 'e')
            ->addSelect('e')
            ->leftJoin('s.organisateur', 'o')
            ->addSelect('o')
            ->leftJoin('s.lieu', 'l')
            ->addSelect('l')
            ->andWhere('e.code in (:etatsVisibles)')
            ->setParameter('etatsVisibles', ['CRE','OUV','FIN','EC'])
            ->orderBy('s.dateHeureDebut', 'ASC');
        return $qb->getQuery()->getResult();
    }

    //Fonction pour tester pagination
    public function findAllNotCanceledPagin(){
        return $this->createQueryBuilder('s')
          ->leftJoin('s.etat', 'e')
            ->addSelect('e')
            ->leftJoin('s.organisateur', 'o')
            ->addSelect('o')
            ->addSelect("
                    CASE
                        WHEN e.code = 'CRE' THEN 1
                        WHEN e.code = 'OUV' THEN 2
                        WHEN e.code = 'FIN' THEN 3
                        ELSE 4
                    END AS HIDDEN ordreEtat
                ")
            ->leftJoin('s.lieu', 'l')
            ->addSelect('l')
            ->andWhere('e.code in (:etatsVisibles)')
            ->setParameter('etatsVisibles', ['CRE','OUV','FIN','EC'])
            ->orderBy('ordreEtat', 'ASC')
            ->addOrderBy('s.dateHeureDebut', 'ASC');

    }
    //    /**
    //     * @return Sortie[] Returns an array of Sortie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sortie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
