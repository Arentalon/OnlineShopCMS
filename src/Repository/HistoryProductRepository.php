<?php

namespace App\Repository;

use App\Entity\HistoryProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HistoryProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoryProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoryProduct[]    findAll()
 * @method HistoryProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoryProduct::class);
    }

    // /**
    //  * @return HistoryProduct[] Returns an array of HistoryProduct objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HistoryProduct
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
