<?php

namespace StravaApi\Service;

use Doctrine\ORM\OptimisticLockException;
use Exception;
use StravaApi\Entity\Activity;
use User\View\Helper\CurrentUser;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use Polyline;

/*
 * Repositories
 */
use StravaApi\Repository\ActivityRepository;
use StravaApi\Repository\RoundRepository;
use StravaApi\Repository\ActivityImportLogRepository;

class StravaService implements StravaServiceInterface
{

    protected $athleteId;
    protected $activitiesPerPage;
    /*
     * @var ActivityRepository
     */
    public $activityRepository;
    /*
     * @var RoundRepository
     */
    public $roundRepository;
    /*
     * @var ActivityImportLogRepository
     */
    public $activityImportLogRepository;

    public function __construct(
        array $config,
        ActivityRepository $activityRepository,
        RoundRepository $roundRepository,
        ActivityImportLogRepository $activityImportLogRepository
    )
    {
        $this->athleteId = $config['stravaSettings']['athleteId'];
        $this->activitiesPerPage = $config['stravaSettings']['activitiesPerPage'];
        $this->activityRepository = $activityRepository;
        $this->roundRepository = $roundRepository;
        $this->activityImportLogRepository = $activityImportLogRepository;
    }

    /**
     * Get athlete from client based on athleteId
     * @param $client
     * @return array
     */
    public function getAthlete($client)
    {
        return $client->getAthlete($this->athleteId);
    }

    /**
     * Get athlete activities from client
     * @param $client
     * @param $after select activities after a specific date
     * @param $page page you want to return
     * @param $per_page how many results per page
     * @return array
     */
    public function getAthleteActivities($client, $before = null, $after = null, $page = null, $per_page = null)
    {
        return $client->getAthleteActivities($before, $after, $page, $per_page);
    }

    /**
     * Get athlete stats from client based
     * @param $client
     * @return array
     */
    public function getAthleteStats($client)
    {
        return $client->getAthleteStats($this->athleteId);
    }

    /**
     * Get specific activity based on activityId
     * @param $client
     * @param $activityId if of the activity
     * @return array
     */
    public function getActivity($client, $activityId = null)
    {
        return $client->getActivity($activityId);
    }

    /**
     * Get all activities from client
     * @param $client
     * @param $after select activities after a specific date
     * @param $page page you want to return
     * @param $per_page how many results per page
     * @return array
     */
    public function getAllActivities($client, $before = null, $after = null, $page = null, $per_page = null)
    {
        //Get athlete stats
        $atheleteStats = $this->getAthleteStats($client);
        //Calculate total activities
        $totalActivities = $atheleteStats["all_ride_totals"]["count"] +
            $atheleteStats["all_run_totals"]["count"] +
            $atheleteStats["all_swim_totals"]["count"];

        //Calaculate total pages based on total activities and activities per page
        $totalPages = ceil($totalActivities / $this->activitiesPerPage);
        //Loop trough all activies based on pages and activities per page and add them to array
        $allActivities = [];
        for ($page = 1; $page <= $totalPages; $page++) {
            $activities = $this->getAthleteActivities($client, null, $after, $page, $this->activitiesPerPage);
            foreach ($activities AS $activity) {
                $activityInDb = $this->activityRepository->getItemByActivityId($activity["id"]);
                if (empty($activityInDb)) {
                    $allActivities[$activity["id"]] = $activity;
                }
            }
        }
        return $allActivities;
    }

    /**
     * Set data to new activity
     * @param $activityArr
     * @param $importLog
     * @param currentUser $currentUser whos is logged on
     * @return      void
     * @throws OptimisticLockException
     */
    public function newActivity($activityArr, $importLog, $currentUser = null)
    {
        $activity = $this->activityRepository->createActivity();
        $activity->setDateCreated(new \DateTime());
        $activity->setCreatedBy($currentUser);
        $activity = $this->setActivity($activityArr, $activity);
        $activity->setActivityImportLog($importLog);
        $activity = $this->activityRepository->storeActivity($activity);
        if (is_object($activity)) {
            return $this->setNewRounds($activityArr["splits_metric"], $activity);
        } else {
            return $activity;
        }
    }

    /**
     * Update data to new activity
     * @param $activityArr
     * @param $activityId
     * @param currentUser $currentUser whos is logged on
     * @return      void
     * @throws OptimisticLockException
     */
    public function updateActivity($activityArr, $activityId, $currentUser = null)
    {
        $activity = $this->activityRepository->findOneBy(['id' => $activityId]);
        $activity->setDateChanged(new \DateTime());
        $activity->setChangedBy($currentUser);
        $activity = $this->setActivity($activityArr, $activity);
        $activity = $this->activityRepository->storeActivity($activity);
        //First remove existing rounds from database
        $rounds = $activity->getRounds();
        $this->removeRounds($rounds);
        if (is_object($activity)) {
            return $this->setNewRounds($activityArr["splits_metric"], $activity);
        } else {
            return $activity;
        }
    }

    /**
     * Set data to existing activity
     * @param activity $activity object
     * @param currentUser $currentUser who is logged on
     * @return      void
     * @throws Exception
     */
    public function setExistingActivity($activity, $currentUser)
    {
        $activity->setDateChanged(new \DateTime());
        $activity->setChangedBy($currentUser);
        $this->activityRepository->storeActivity($activity);
    }


    /**
     * Set data to existing activity object
     * @param array $activityArr
     * @param object $activity
     * @return activity object
     * @throws Exception
     */
    private function setActivity($activityArr, $activity)
    {
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

        return $activity;
    }

    /**
     * Save rounds to db
     * @return      boolean
     * @throws OptimisticLockException
     * @var $rounds array with rounds
     * @var activity to connect rounds to
     */
    private function setNewRounds($rounds, $activity)
    {
        $result = true;
        if (count($rounds) > 0) {
            foreach ($rounds as $item) {
                $round = $this->roundRepository->createRound();
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
                $result = $this->roundRepository->storeRound($round);
                if ($result == false) {
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * Remove rounds from db
     * @return      boolean
     * @throws OptimisticLockException
     * @var $rounds array with round objects
     */
    private function removeRounds($rounds)
    {
        $result = true;
        if (count($rounds) > 0) {
            foreach ($rounds AS $round) {
                $result = $this->roundRepository->removeRound($round);
                if ($result == false) {
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * Get array of activities for pagination
     * @param string $query
     * @param int $currentPage
     * @param int $itemsPerPage
     * @return Paginator
     */
    public function getItemsForPagination($query, $currentPage = 1, $itemsPerPage = 10)
    {
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage($itemsPerPage);
        $paginator->setCurrentPageNumber($currentPage);
        return $paginator;
    }

    /**
     * Get start and end date based on given year and/or month
     * @return array
     * @throws Exception
     * @var $year year
     * @var $month month
     */
    public function getStartAndEndDateByMonthAndYear($year = null, $month = null)
    {
        if ($year == null) {
            $year = new \DateTime('now');
            $startDate = new \DateTime($year->format('Y') . '-' . $month . '-01');
            $endDate = clone $startDate;
        } else if ($month == null) {
            $startDate = new \DateTime($year . '-01-01');
            $endDate = new \DateTime($year . '-12-01');
        } else {
            $startDate = new \DateTime($year . '-' . $month . '-01');
            $endDate = clone $startDate;
        }
        $startDate->modify('first day of this month');
        $endDate->modify('last day of this month');
        return [
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
    }

    /**
     * Decode a summary poly line to points
     * @param $summaryPolyLine
     * @return array
     */
    public function getPolyLinePoints($summaryPolyLine)
    {
        $points = Polyline::decode($summaryPolyLine);
        return Polyline::pair($points);
    }

    /**
     * Returns an array of months
     * @return array
     */
    public function getMonths()
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maart',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Augustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'December'
        ];
    }
}
