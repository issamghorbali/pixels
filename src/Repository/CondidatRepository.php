<?php

namespace App\Repository;

use App\Entity\Condidat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Condidat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Condidat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Condidat[]    findAll()
 * @method Condidat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CondidatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Condidat::class);
    }

    // /**
    //  * @return Condidat[] Returns an array of Condidat objects
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
    public function findOneBySomeField($value): ?Condidat
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
