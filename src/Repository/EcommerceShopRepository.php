<?php

namespace App\Repository;

use App\Entity\EcommerceWebsite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EcommerceWebsite|null find($id, $lockMode = null, $lockVersion = null)
 * @method EcommerceWebsite|null findOneBy(array $criteria, array $orderBy = null)
 * @method EcommerceWebsite[]    findAll()
 * @method EcommerceWebsite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EcommerceShopRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EcommerceWebsite::class);
    }

    public function nextPort(){

    }

//    /**
//     * @return EcommerceShop[] Returns an array of EcommerceShop objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EcommerceShop
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
