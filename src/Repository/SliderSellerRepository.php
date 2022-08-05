<?php

namespace App\Repository;

use App\Entity\SliderSeller;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SliderSeller|null find($id, $lockMode = null, $lockVersion = null)
 * @method SliderSeller|null findOneBy(array $criteria, array $orderBy = null)
 * @method SliderSeller[]    findAll()
 * @method SliderSeller[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SliderSellerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SliderSeller::class);
    }

    // /**
    //  * @return SliderSeller[] Returns an array of SliderSeller objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SliderSeller
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
