<?php

namespace App\Repository;

use App\Entity\Category;
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

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findProductsByCategory(string  $productName , Category $category)
    {
        return $this->createQueryBuilder('p')
            ->where('p.name LIKE :name')->setParameter('name','%'.$productName.'%')
            ->andWhere('p.category = :category')
            ->setParameter('category', $category)
            ->orderBy('p.createdAt','DESC')
            ->getQuery()->getResult()
        ;
    }

    public function findByLikeName($productName)
    {
        return $this->createQueryBuilder('p')
            ->where('p.name LIKE :name')->setParameter('name', '%' . $productName . '%')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()->getResult();


    }

	public function test($productName)
	{
		return $this->createQueryBuilder('p')

			->join('p.cartLines', 'c')
			->join('p.variants', 'v')
			->GroupBy('p')
			->orderBy('count(p)')
			->getQuery()
			->getResult();


    }

}
