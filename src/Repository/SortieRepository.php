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


    public function findByFilters(SortieFilterDTO $filters, User $user): array
    {

        $qb = $this->createQueryBuilder('s');

        if ($filters->inputSearch) {
            $qb->andWhere('s.nom LIKE :search')
                ->setParameter('search', '%'.$filters->inputSearch.'%');
        }
        if ($filters->isOrganisateur) {
            $qb->andWhere('s.organisateur = :user')
                ->setParameter('user', $user->getId());
        }
        if ($filters->isInscrit) {
            $qb->join('s.inscriptions', 'i')
                ->andWhere('i.participant = :user')
                ->setParameter('user', $user);
        }
        if ($filters->isNotInscrit && $user) {
            $qb->leftJoin('s.inscriptions', 'i2', 'WITH', 'i2.participant = :user')
                ->andWhere('i2.id IS NULL')
                ->setParameter('user', $user);
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
        if($filters->ended) {
            $qb->andWhere('s.dateLimiteInscription > :dateJour')
                ->setParameter('dateJour', $filters->ended);
        }

        return $qb->getQuery()->getResult();
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
