<?php

namespace StravaApi\Repository;

use Doctrine\ORM\EntityRepository;
use StravaApi\Entity\Activity;

class ActivityRepository extends EntityRepository
{
    /**
     * Get activity by activity id
     * @return object
     * @var $activityId
     */
    public function getItemByActivityId($activityId = null)
    {
        return $this->findOneBy(['activityId' => $activityId], []);
    }

    /**
     * Get all activities ordered by start date desc
     * @return array
     */
    public function getAllActivities()
    {
        return $this->findBy([], ['startDate' => 'DESC']);
    }

    /**
     * Get total activities
     * @param $type type of activity (exampl. Run or Ride)
     * @return      integer
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTotalItems($type = null)
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
     * @return      integer
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
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
     * @return      integer
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
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
     * @return      integer
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
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
     * @return integer
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
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
     * @return      integer
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
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
     * @param int $workoutType type of workout 1 = Competition 13 = Training
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
     * @param int $workoutType type of workout 1 = Competition 13 = Training
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

    /**
     * Get all activities
     * @return \Doctrine\ORM\Query
     */
    public function getActivities()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.startDate', 'DESC')
            ->getQuery();
    }

    public function filterActivities($data)
    {

        $qb = $this->createQueryBuilder('a');
        $orX = $qb->expr()->orX();
        //Search on workout title
        if (!empty($data->title)) {
            $orX->add($qb->expr()->like('a.name', "'%$data->title%'"));
        }
        //Search on training or game
        if (!empty($data->workoutType)) {
            $orX->add($qb->expr()->eq('a.workoutType', $data->workoutType));
        }
        //Search on year
        if (!empty($data->year)) {
            $orX->add($qb->expr()->eq('YEAR(a.startDate)', $data->year));
        }

        //Search on period
        if (!empty($data->periodFrom) || !empty($data->periodTo)) {
            if (!empty($data->periodFrom) && !empty($data->periodTo)) {
                $periodFrom = new \DateTime($data->periodFrom);
                $periodTo = new \DateTime($data->periodTo);
                $andX = $qb->expr()->between('a.startDate', ':periodFrom', ':periodTo');
                $qb->setParameter('periodFrom', $periodFrom->format('Y-m-d'));
                $qb->setParameter('periodTo', $periodTo->format('Y-m-d'));
                $orX->add($andX);
            } elseif (!empty($data->periodFrom) && empty($data->periodTo)) {
                $periodFrom = new \DateTime($data->periodFrom);
                $orX->add($qb->expr()->gte('a.startDate', $periodFrom->format('Y-m-d')));
            } elseif (empty($data->periodFrom) && !empty($data->periodTo)) {
                $periodTo = new \DateTime($data->periodTo);
                $orX->add($qb->expr()->lte('a.startDate', $periodTo->format('Y-m-d')));
            }
        }

        //Search on kilometers
        if (!empty($data->kmFrom) || !empty($data->kmTo)) {
            $kmFrom = null;
            $kmTo = null;
            if (!empty($data->kmFrom)) {
                $kmFrom = $data->kmFrom * 1000;
            }
            if (!empty($data->kmTo)) {
                $kmTo = $data->kmTo * 1000;
            }
            if (!empty($data->kmFrom) && !empty($data->kmTo)) {
                $andX = $qb->expr()->andX();
                $andX->add($qb->expr()->gte('a.distance', $kmFrom));
                $andX->add($qb->expr()->lte('a.distance', $kmTo));
                $orX->add($andX);
            } elseif (!empty($data->kmFrom) && empty($data->kmTo)) {
                $orX->add($qb->expr()->gte('a.distance', $kmFrom));
            } elseif (empty($data->kmFrom) && !empty($data->kmTo)) {
                $orX->add($qb->expr()->lte('a.distance', $kmTo));
            }
        }

        //Search on tempo
        if (!empty($data->tempoFrom) || !empty($data->tempoTo)) {


            if (!empty($data->tempoFrom) && !empty($data->tempoTo)) {


                $periodFrom = new \DateTime($data->tempoFrom);
                $periodTo = new \DateTime($data->tempoTo);
                $andX = $qb->expr()->between('a.averageSpeedTime', ':periodFrom', ':periodTo');
                $qb->setParameter('periodFrom', $periodFrom->format('H:i:s'));
                $qb->setParameter('periodTo', $periodTo->format('H:i:s'));
                $orX->add($andX);
            } elseif (!empty($data->tempoFrom) && empty($data->tempoTo)) {
                $periodFrom = new \DateTime($data->tempoFrom);
                $orX->add($qb->expr()->gte('a.averageSpeedTime', $periodFrom->format('H:i:s')));
            } elseif (empty($data->tempoFrom) && !empty($data->tempoTo)) {
                $periodTo = new \DateTime($data->tempoTo);
                $orX->add($qb->expr()->lte('a.averageSpeedTime', $periodTo->format('H:i:s')));
            }
        }

        //Search on hearthbeat
        if (!empty($data->hearthRateFrom) || !empty($data->hearthRateTo)) {
            if (!empty($data->hearthRateFrom) && !empty($data->hearthRateTo)) {
                $andX = $qb->expr()->andX();
                $andX->add($qb->expr()->gte('a.averageHeartrate', $data->hearthRateFrom));
                $andX->add($qb->expr()->lte('a.averageHeartrate', $data->hearthRateTo));
                $orX->add($andX);
            } elseif (!empty($data->hearthRateFrom) && empty($data->hearthRateTo)) {
                $orX->add($qb->expr()->gte('a.averageHeartrate', $data->hearthRateFrom));
            } elseif (empty($data->hearthRateFrom) && !empty($data->hearthRateTo)) {
                $orX->add($qb->expr()->lte('a.averageHeartrate', $data->hearthRateTo));
            }
        }

        //Search on height difference
        if (!empty($data->elevationGainFrom) || !empty($data->elevationGainTo)) {
            if (!empty($data->elevationGainFrom) && !empty($data->elevationGainTo)) {
                $andX = $qb->expr()->andX();
                $andX->add($qb->expr()->gte('a.totalElevationGain', $data->elevationGainFrom));
                $andX->add($qb->expr()->lte('a.totalElevationGain', $data->elevationGainTo));
                $orX->add($andX);
            } elseif (!empty($data->elevationGainFrom) && empty($data->elevationGainTo)) {
                $orX->add($qb->expr()->gte('a.totalElevationGain', $data->elevationGainFrom));
            } elseif (empty($data->elevationGainFrom) && !empty($data->elevationGainTo)) {
                $orX->add($qb->expr()->lte('a.totalElevationGain', $data->elevationGainTo));
            }
        }

        $qb->where($orX);
        return $qb->getQuery();
    }

    /**
     * Search activities by params
     * @param $data
     * @return \Doctrine\ORM\Query
     */
    public function searchActivities($data)
    {
        $result = $this->createQueryBuilder('a')
            ->where('a.startDate BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $data->format('Y-m-d'))
            ->setParameter('endDate', $data->format('Y-m-d'))
            ->orderBy('a.startDate', 'ASC')
            ->getQuery()
            ->getArrayResult();
        $data['activities'] = $result;

        return $data;
    }

    /**
     * Get all activities between two dates
     * @param $startDate object
     * @param $endDate object
     * @return array
     */
    public function getActivityBetweenDates($startDate = null, $endDate = null)
    {
        $result = $this->createQueryBuilder('a')
            ->where('a.startDate BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate->format('Y-m-d'))
            ->setParameter('endDate', $endDate->format('Y-m-d'))
            ->orderBy('a.startDate', 'ASC')
            ->getQuery()
            ->getArrayResult();
        $data['activities'] = $result;

        return $data;
    }

    /**
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
        return intval((($distance / 1000) * 100)) / 100;
    }


    /**
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
     * @params      $activity object
     * @return      object
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

    /**
     * Delete a Activity object from database
     * @param activity $activity object
     * @return bool
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeActivity($activity)
    {
        try {
            $this->getEntityManager()->remove($activity);
            $this->getEntityManager()->flush();
            return true;
        } catch (\Doctrine\DBAL\DBALException $e) {
            return false;
        }
    }

}
