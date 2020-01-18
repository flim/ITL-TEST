<?php

namespace App\Repository;

use App\Entity\BaseCompany;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BaseCompany|null find($id, $lockMode = null, $lockVersion = null)
 * @method BaseCompany|null findOneBy(array $criteria, array $orderBy = null)
 * @method BaseCompany[]    findAll()
 * @method BaseCompany[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BaseCompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BaseCompany::class);
    }

    // /**
    //  * @return BaseCompany[] Returns an array of BaseCompany objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BaseCompany
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
