<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findProducts(
        int $categoryId = null, 
        array $sortType = null, 
        string $queryString = null, 
        int $page = null, 
        int $limit = null,
        bool $isAdmin = false
    ) {
        $builder = $this->createQueryBuilder('p');

        if ($categoryId !== null) {
            $builder->andWhere('p.categoryId = :categoryId');
            $builder->setParameter('categoryId', $categoryId);
        }

        if ($sortType !== null) {
            $val = current($sortType);
            $key = key($sortType);
            $builder->orderBy('p.' . $key, $val);
        } 

        if ($queryString !== null) {
            $builder->andWhere('LOWER(p.name) LIKE :searchString');
            $builder->setParameter('searchString', '%' . strtolower($queryString) . '%');
        } 

        if ($page !== null && $limit != null) {
            $offset = $limit * $page;
            $builder->setFirstResult($offset);
            $builder->setMaxResults($limit);
        }

        if (!$isAdmin) {
            $builder->andWhere('p.isActive = 1');
            $builder->andWhere('p.amount > 0');
            $builder->andWhere('p.startDate < :now AND ((p.endDate IS NOT NULL AND p.endDate > :now) OR p.endDate IS NULL)');
            $builder->setParameter('now', new \DateTime());
        }

        $products = $builder->getQuery()->getResult() ?? [];
        $builder->select('COUNT(p.productId)');
        $builder->setFirstResult(null);
        $builder->setMaxResults(null);
        $count = $builder->getQuery()->getSingleScalarResult();

        return [$products, $count];
    }

//    public function search($key)
//    {
//        return $this->createQueryBuilder('p')
//             ->andWhere('p.name like :key')
//             ->setParameter('key', '%' . $key . '%')
//             ->getQuery()
//             ->getResult()
//         ;
//    }

//
//    public function findSorted(string $key)
//    {
//        return $this->findBy()
//    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    
    // public function findByExampleField($value)
    // {
    //     return $this->createQueryBuilder('p')
    //         ->andWhere('p.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->orderBy('p.id', 'ASC')
    //         ->setMaxResults(10)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }
    

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
