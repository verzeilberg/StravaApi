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
        $result =  $this->createQueryBuilder('a')
            ->where('a.startDate BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate->format('Y-m-d'))
            ->setParameter('endDate', $endDate->format('Y-m-d'))
            ->orderBy('a.startDate', 'ASC')
            ->getQuery()
            ->getResult();
        $data = [];
        $data1 = [];
        $data2 = [];
        $labels = [];
        $totalDistance = 0;
        foreach($result AS $result) {
            $labels[] = $result->getStartDate()->format('d-m-Y');
            $data1[] = $this->getDistance($result->getDistance());
            $totalDistance = $totalDistance + $this->getDistance($result->getDistance());
            $data2[] = $totalDistance;
        }

        $data['kmPerActivity'] = $data1;
        $data['kmActivityCumulatief'] = $data2;
        $data['labels'] = $labels;

        return $data;
    }

    private function getDistance($distance)
    {
        return intval((($distance / 1000) *100))/100;
    }


    /*
     * Get years from activities
     *
     * @return array
     *
     */
    public function getYearsByActivities()
    {
        return $this->createQueryBuilder('a')
            ->select('YEAR(a.startDate) as jaar')
            ->groupBy('YEAR(a.startDate)')
            ->getQuery()
            ->getResult();
    }
}