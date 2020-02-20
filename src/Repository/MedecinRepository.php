<?php

namespace App\Repository;

use App\Entity\medecin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method medecin|null find($id, $lockMode = null, $lockVersion = null)
 * @method medecin|null findOneBy(array $criteria, array $orderBy = null)
 * @method medecin[]    findAll()
 * @method medecin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedecinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, medecin::class);
    }

    // /**
    //  * @return medecin[] Returns an array of medecin objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?medecin
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
