<?php

namespace App\Repository;

use App\Entity\CartItems;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CartItems|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartItems|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartItems[]    findAll()
 * @method CartItems[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartItemsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartItems::class);
    }

    // /**
    //  * @return CartItems[] Returns an array of CartItems objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CartItems
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
