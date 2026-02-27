<?php

namespace App\Repository;

use App\DTO\SortieFilterDTO;

use App\DTO\VilleFilterDTO;
use App\Entity\Ville;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ville>
 */
class VilleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ville::class);

    }
    public function findByFilters(VilleFilterDTO $filters,int $limit, int $offset): array
    {
        $query = $this->createQueryBuilder('ville');

        if ($filters->inputSearch) {
            $query->andWhere('ville.nom LIKE :search')
                ->setParameter('search', '%'.$filters->inputSearch.'%');
        }
        $query2 = clone $query;
        $query2->select('COUNT(ville.id)');

        return  [
            $query2->getQuery()->getSingleScalarResult(),
            $query->setFirstResult($offset)
                ->setMaxResults($limit)
                ->getQuery()->getResult()
        ];
    }

    public function findVilleOrderByNom(int $limit, int $offset): array {
        $query = $this->createQueryBuilder('v')
            ->orderBy('v.nom', 'ASC');

        $query2 = clone $query;
        $query2->select('COUNT(v.id)');

        return [
            $query2->getQuery()->getSingleScalarResult(),
            $query->setFirstResult($offset)
                ->setMaxResults($limit)
                ->getQuery()->getResult()
        ];
    }

    //    /**
    //     * @return Ville[] Returns an array of Ville objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Ville
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
