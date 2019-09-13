<?php

namespace StravaApi\Repository;

use Doctrine\ORM\EntityRepository;
use StravaApi\Entity\Activity;

class ActivityRepository extends EntityRepository
{
    /*
     * Get activity by activity id
     * @var $activityId
     * @return object
     */
    public function getItemByActivityId($activityId = null)
    {
        return $this->findOneBy(['activityId' => $activityId], []);
    }

    public function getAllActivities()
    {
        return $this->findBy([],['startDate' => 'DESC']);
    }

    /*
     * Get total activites
     * @param $type type of activity (exampl. Run or Ride)
     * @return      integer
     */
    public function getTotalActivities($type = null)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('COUNT(a.id) as total');
        if (!empty($type)) {
            $qb->where('a.type = :type');
            $qb->setParameter('type', $type);
        }
        $query = $qb->getQuery();
        $result = $query->getSingleResult();

        return (int)$result['total'];

    }

    /**
     * Get total distance
     * @param $type type of activity (exampl. Run or Ride)
     *
     * @return      integer
     */
    public function getTotalDistance($type = null)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('SUM(a.distance) as distance');
        if (!empty($type)) {
            $qb->where('a.type = :type');
            $qb->setParameter('type', $type);
        }
        $query = $qb->getQuery();
        $result = $query->getSingleResult();
        return $result['distance'];
    }

    /**
     * Get total time
     * @param $type type of activity (exampl. Run or Ride)
     *
     * @return      integer
     */
    public function getTotalTime($type = null)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('SUM(a.movingTime) as movingTime');
        if (!empty($type)) {
            $qb->where('a.type = :type');
            $qb->setParameter('type', $type);
        }
        $query = $qb->getQuery();
        $result = $query->getSingleResult();
        return $result['movingTime'];
    }

    /**
     * Get average speed
     * @param $type type of activity (exampl. Run or Ride)
     *
     * @return      integer
     */
    public function getAverageSpeed($type = null)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('SUM(a.averageSpeed) as average_speed, COUNT(a.id) as total_runs');
        if (!empty($type)) {
            $qb->where('a.type = :type');
            $qb->setParameter('type', $type);
        }
        $query = $qb->getQuery();
        $result = $query->getSingleResult();
        return $result['average_speed'] / $result['total_runs'];
    }

    /**
     * Get average elevation
     * @param $type type of activity (exampl. Run or Ride)
     *
     * @return integer
     */
    public function getAverageElevation($type = null)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('SUM(a.totalElevationGain) as total_elevation_gain, COUNT(a.id) as total_runs');
        if (!empty($type)) {
            $qb->where('a.type = :type');
            $qb->setParameter('type', $type);
        }
        $query = $qb->getQuery();
        $result = $query->getSingleResult();
        return $result['total_elevation_gain'] / $result['total_runs'];
    }


    /**
     * Get average hearthbeat
     * @param $type type of activity (exampl. Run or Ride)
     *
     * @return      integer
     */
    public function getAverageHeartbeat($type = null)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('SUM(a.averageHeartrate) as average_heartrate, COUNT(a.id) as total_runs');
        if (!empty($type)) {
            $qb->where('a.type = :type');
            $qb->setParameter('type', $type);
        }
        $query = $qb->getQuery();
        $result = $query->getSingleResult();
        return $result['average_heartrate'] / $result['total_runs'];
    }

    /**
     * Get longest activity
     * @param $type type of activity (exampl. Run or Ride)
     * @param $workoutType type of workout 1 = Competition 13 = Training
     *
     * @return      array
     */
    public function getLongestActivity($type = null, $workoutType = 1)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.workoutType = :workOut');
        if (!empty($type)) {
            $qb->andWhere('a.type = :type');
            $qb->setParameter('type', $type);
        }
        $qb->setParameter('workOut', $workoutType);
        $qb->orderBy('a.distance', 'DESC');
        $qb->setMaxResults(1);
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result[0];
    }

    /**
     * Get fastest activity
     * @param $type type of activity (exampl. Run or Ride)
     * @param $workoutType type of workout 1 = Competition 13 = Training
     *
     * @return      array
     */
    public function getFastestActivity($type = null, $workoutType = 1)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.workoutType = :workOut');
        if (!empty($type)) {
            $qb->andWhere('a.type = :type');
            $qb->setParameter('type', $type);
        }
        $qb->setParameter('workOut', $workoutType);
        $qb->orderBy('a.averageSpeed', 'DESC');
        $qb->setMaxResults(1);
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result[0];
    }

    /*
     * Get all activities
     *
     * @return query
     */
    public function getActivities()
    {
        return $this->createQueryBuilder('a')
                ->orderBy('a.startDate', 'DESC')
                ->getQuery();
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
            ->getArrayResult();
        $data['activities'] = $result;

        return $data;
    }

    /*
     * Get activity based on id
     * @params $id
     * @return object
     */
    public function getActivityById($id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    private function getDistance($distance)
    {
        return intval((($distance / 1000) *100))/100;
    }


    /*
     * Get unique years from activities
     * @return array
     */
    public function getYearsByActivities()
    {
        return $this->createQueryBuilder('a')
            ->select('YEAR(a.startDate) as jaar')
            ->groupBy('jaar')
            ->getQuery()
            ->getScalarResult();
    }

    /**
     * Create a new Activity object
     * @return      object
     */
    public function createActivity()
    {
        return new Activity();
    }
    /*
 * Save activity to database
 *
 * @params      $activity object
 * @return      object
 *
 */
    public function storeActivity($activity)
    {
        try {
            $this->getEntityManager()->persist($activity);
            $this->getEntityManager()->flush();
            return $activity;
        } catch (\Doctrine\DBAL\DBALException $e) {
            return false;
        }
    }

    /*
     * Delete a Activity object from database
     *
     * @param       activity $activity object
     * @return      void
     *
     */
    public function deleteActivity($activity)
    {
        $this->getEntityManager()->remove($activity);
        $this->getEntityManager()->flush();
    }

}