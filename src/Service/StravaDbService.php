<?php

namespace StravaApi\Service;

use StravaApi\Entity\Activity;
use StravaApi\Entity\Round;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

class StravaDbService implements StravaDbServiceInterface
{

    protected $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

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
        $activity = $this->entityManager->getRepository(Activity::class)
            ->findOneBy(['id' => $id], []);

        return $activity;
    }


    public function loopTroughActivities($items)
    {
        foreach ($items as $item) {
            $activity = $this->createActivity();
            $this->setNewActivity($item);
        }
    }

    /**
     *
     * Save activity to database
     *
     * @param       activity object
     * @return      void
     *
     */
    public function storeActivity($activity)
    {
        try {
            $this->entityManager->persist($activity);
            $this->entityManager->flush();
            return $activity;
        } catch (\Doctrine\DBAL\DBALException $e) {
            return false;
        }
    }

    /**
     *
     * Delete a Activity object from database
     * @param       activity $activity object
     * @return      object
     *
     */
    public function deleteActivity($activity)
    {
        $this->entityManager->remove($activity);
        $this->entityManager->flush();
    }

    /**
     *
     * Set data to new activity
     *
     * @param       activity $activity object
     * @param       currentUser $currentUser whos is logged on
     * @return      void
     *
     */
    public function setNewActivity($activityArr, $importLog, $currentUser = null)
    {
        $activity = $this->createActivity();
        $activity->setDateCreated(new \DateTime());
        $activity->setCreatedBy($currentUser);
        $activity->setActivityId((int)$activityArr['id']);
        $activity->setAthleteId($activityArr["athlete"]["id"]);
        $activity->setName($activityArr["name"]);
        $activity->setDistance($activityArr["distance"]);
        $activity->setMovingTime($activityArr["moving_time"]);
        $activity->setElapsedTime($activityArr["elapsed_time"]);
        $activity->setTotalElevationGain($activityArr["total_elevation_gain"]);
        $activity->setType($activityArr["type"]);
        $startDate = new \DateTime($activityArr["start_date"]);
        $activity->setStartDate($startDate);
        $startDateLocal = new \DateTime($activityArr["start_date_local"]);
        $activity->setStartDateLocal($startDateLocal);
        $activity->setTimezone($activityArr["timezone"]);
        $activity->setStartLat($activityArr["start_latlng"][0]);
        $activity->setStartLng($activityArr["start_latlng"][1]);
        $activity->setEndLat($activityArr["end_latlng"][0]);
        $activity->setEndLng($activityArr["end_latlng"][1]);
        $activity->setSummaryPolyline($activityArr["map"]["polyline"]);
        $activity->setAverageSpeed($activityArr["average_speed"]);
        $activity->setMaxSpeed($activityArr["max_speed"]);
        $activity->setAverageHeartrate($activityArr["average_heartrate"]);
        $activity->setMaxHeartrate($activityArr["max_heartrate"]);
        $activity->setElevHigh($activityArr["elev_high"]);
        $activity->setElevLow($activityArr["elev_low"]);
        $activity->setDescription($activityArr["description"]);
        $activity->setWorkoutType($activityArr["workout_type"]);
        $activity->setActivityImportLog($importLog);

        $activity = $this->storeActivity($activity);

        if (is_object($activity)) {
            return $this->setNewRounds($activityArr["splits_metric"], $activity);
        } else {
            return $activity;
        }
    }


    /**
     *
     * Get total activites
     * @param $type type of activity (exampl. Run or Ride)
     *
     * @return      integer
     *
     */
    public function getTotalActivities($type = null)
    {
        $qb = $this->entityManager->getRepository(Activity::class)->createQueryBuilder('a');
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
     *
     * Get total distance
     * @param $type type of activity (exampl. Run or Ride)
     *
     * @return      integer
     *
     */
    public function getTotalDistance($type = null)
    {
        $qb = $this->entityManager->getRepository(Activity::class)->createQueryBuilder('a');
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
     *
     * Get total distance
     * @param $type type of activity (exampl. Run or Ride)
     *
     * @return      integer
     *
     */
    public function getTotalTime($type = null)
    {
        $qb = $this->entityManager->getRepository(Activity::class)->createQueryBuilder('a');
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
     *
     * Get average speed
     * @param $type type of activity (exampl. Run or Ride)
     *
     * @return      integer
     *
     */
    public function getAverageSpeed($type = null)
    {
        $qb = $this->entityManager->getRepository(Activity::class)->createQueryBuilder('a');
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
     *
     * Get average elevation
     * @param $type type of activity (exampl. Run or Ride)
     *
     * @return      integer
     *
     */
    public function getAverageElevation($type = null)
    {
        $qb = $this->entityManager->getRepository(Activity::class)->createQueryBuilder('a');
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
     *
     * Get average hearthbeat
     * @param $type type of activity (exampl. Run or Ride)
     *
     * @return      integer
     *
     */
    public function getAverageHeartbeat($type = null)
    {
        $qb = $this->entityManager->getRepository(Activity::class)->createQueryBuilder('a');
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
     *
     * Get fastest activity
     * @param $type type of activity (exampl. Run or Ride)
     * @param $workoutType type of workout 1 = Competition 13 = Training
     *
     * @return      array
     *
     */
    public function getFastestActivity($type = null, $workoutType = 1)
    {
        $qb = $this->entityManager->getRepository(Activity::class)->createQueryBuilder('a');
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
     *
     * Get fastest round
     * @param $type type of activity (exampl. Run or Ride)
     * @param $workoutType type of workout 1 = Competition 13 = Training
     *
     * @return      array
     *
     */
    public function getFastestRound($type = null, $workoutType = 1)
    {
        $qb = $this->entityManager->getRepository(Round::class)->createQueryBuilder('r');
        $qb->leftJoin('r.activity a');
        $qb->where('a.workoutType = :workOut');
        $qb->andWhere('r.movingTime > 0');
        if (!empty($type)) {
            $qb->andWhere('a.type = :type');
            $qb->setParameter('type', $type);
        }
        $qb->setParameter('workOut', $workoutType);
        $qb->orderBy('r.movingTime', 'ASC');
        $qb->setMaxResults(1);
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result[0];

    }


    /**
     *
     * Get longest activity
     * @param $type type of activity (exampl. Run or Ride)
     * @param $workoutType type of workout 1 = Competition 13 = Training
     *
     * @return      array
     *
     */
    public function getLongestActivity($type = null, $workoutType = 1)
    {
        $qb = $this->entityManager->getRepository(Activity::class)->createQueryBuilder('a');
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
     *
     * Set data to existing activity
     *
     * @param       activity $activity object
     * @param       currentUser $currentUser whos is logged on
     * @return      void
     *
     */
    public function setExistingActivity($activity, $currentUser)
    {
        $activity->setDateChanged(new \DateTime());
        $activity->setChangedBy($currentUser);
        $this->storeActivity($activity);
    }

    /**
     *
     * Create a new Activity object
     *
     * @return      object
     *
     */
    public function createActivity()
    {
        return new Activity();
    }

    /**
     *
     * Create a new Round object
     *
     * @return      object
     *
     */
    public function createRound()
    {
        return new Round();
    }

    /**
     *
     * Save rounds to db
     *
     * @var $rounds array with rounds
     * @var activity to connect rounds to
     *
     * @return      boolean
     *
     */
    public function setNewRounds($rounds, $activity)
    {
        $result = true;
        if (count($rounds) > 0) {
            foreach ($rounds as $item) {
                $round = $this->createRound();
                $round->setDistance($item["distance"]);
                $round->setElapsedTime($item["elapsed_time"]);
                $round->setElevationDifference($item["elevation_difference"]);
                $round->setMovingTime($item["moving_time"]);
                $round->setSplit($item["split"]);
                $round->setAverageSpeed($item["average_speed"]);
                $round->setAverageHeartrate($item["average_heartrate"]);
                $round->setPaceZone($item["pace_zone"]);
                $round->setActivity($activity);
                $round->setDateCreated(new \DateTime());
                $round->setCreatedBy(null);

                $result = $this->storeRound($round);
                if ($result == false) {
                    break;
                }
            }
        }
        return $result;
    }

    /**
     *
     * Save round to database
     *
     * @param       round object
     * @return      void
     *
     */
    public function storeRound($round)
    {
        try {
            $this->entityManager->persist($round);
            $this->entityManager->flush();
            return true;
        } catch (\Doctrine\DBAL\DBALException $e) {
            \Doctrine\Common\Util\Debug::dump($e);
            die('fgdg');

            return false;
        }
    }

    /**
     *
     * Get array of activitys
     *
     * @return      query
     *
     */
    public function getItems()
    {
        $qb = $this->entityManager->getRepository(Activity::class)->createQueryBuilder('a')
            ->orderBy('a.startDate', 'DESC');
        return $qb->getQuery();
    }

    /**
     *
     * Get activity by activity id
     * @var $activityId
     * @return      object
     *
     */
    public function getItemByActivityId($activityId = null)
    {
        $activity = $this->entityManager->getRepository(Activity::class)
            ->findOneBy(['activityId' => $activityId], []);

        return $activity;
    }

    /**
     *
     * Get array of activities  for pagination
     * @var $query query
     * @var $currentPage current page
     * @var $itemsPerPage items on a page
     *
     * @return array
     *
     */
    public function getItemsForPagination($query, $currentPage = 1, $itemsPerPage = 10)
    {
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage($itemsPerPage);
        $paginator->setCurrentPageNumber($currentPage);
        return $paginator;
    }

    public function getStartAndEndDateByMonthAndYear($year = null, $month = null)
    {

        if ($year === null) {
            $year = new \DateTime('now');
            $startDate = new \DateTime($year->format('Y') .'-'.$month.'-01');
            $endDate = clone $startDate;
        } else if ($month === null) {
            $month = new \DateTime('now');
            $startDate = new \DateTime($year .'-'.$month->format('m').'-01');
            $endDate = clone $startDate;
        } else {
            $startDate = new \DateTime($year .'-'.$month.'-01');
            $endDate = clone $startDate;
        }

        $startDate->modify('first day of this month');
        $endDate->modify('last day of this month');

        return [
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
    }

    /*
     * Get years from activities
     *
     * @return array
     *
     */
    public function getYearsByActivities()
    {
        return $this->entityManager->getRepository(Activity::class)->createQueryBuilder('a')
            ->select('YEAR(a.startDate) as jaar')
            ->groupBy('jaar')
            ->getQuery()
            ->getScalarResult();
    }

}