<?php

namespace App\Repository;

use App\Entity\UserS;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserS|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserS|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserS[]    findAll()
 * @method UserS[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserS::class);
    }

    // /**
    //  * @return UserS[] Returns an array of UserS objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserS
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
