<?php

namespace StravaApi\Repository;

use Doctrine\ORM\EntityRepository;

class ActivityRepository extends EntityRepository
{

    /*
 * Get activity based on id
 *
 * @params $id
 *
 * @return object
 *
 */
    public function getActivityById($id)
    {
        return $this->findOneBy(['id' => $id], []);
    }

    /*
     * Get all activities between two dates
     *
     * @param $startDate object
     * @param $endDate object
     *
     * @return array
     *
     */
    public function getActivityBetweenDates($startDate = null, $endDate = null)
    {
        return $this->createQueryBuilder('a')
            ->where('a.startDate BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate->format('Y-m-d'))
            ->setParameter('endDate', $endDate->format('Y-m-d'))
            ->orderBy('a.startDate', 'ASC')
            ->getQuery()
            ->getResult();

    }
}