<?php
namespace StravaApi\Repository;

use Doctrine\ORM\EntityRepository;

class RoundRepository extends EntityRepository
{

    /**
    * @return Round[] Returns an array of Article objects
    */

    public function findFastestRound()
    {
        return true;
    }
    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}