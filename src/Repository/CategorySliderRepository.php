<?php

namespace App\Repository;

use App\Entity\CategorySlider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategorySlider|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorySlider|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorySlider[]    findAll()
 * @method CategorySlider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method findByCategory(\App\Entity\Category $category)
 */
class CategorySliderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorySlider::class);
    }

    // /**
    //  * @return CategorySlider[] Returns an array of CategorySlider objects
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
    public function findOneBySomeField($value): ?CategorySlider
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
