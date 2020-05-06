<?php

namespace StravaApi\Service;

use Doctrine\ORM\OptimisticLockException;
use Exception;
use StravaApi\Entity\Activity;
use User\View\Helper\CurrentUser;
use Laminas\ServiceManager\ServiceLocatorInterface;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Laminas\Paginator\Paginator;
use Polyline;

/*
 * Repositories
 */
use StravaApi\Repository\ActivityRepository;
use StravaApi\Repository\RoundRepository;
use StravaApi\Repository\ActivityImportLogRepository;

class StravaService
{

    /**
     * @var athleteId
     */
    protected $athleteId;
    /**
     * @var activitiesPerPage
     */
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

    //-------------------------------| Activities |-------------------------------//

    /**
     * Get Activity
     * @param $accessToken
     * @param null $activityId
     * @param int $include_all_efforts
     * @return array|mixed
     */
    public function getActivity($accessToken, $activityId = null, $include_all_efforts = 1)
    {
        $data = array(
            'include_all_efforts' => $include_all_efforts
        );
        $curl = curl_init();
        $header = [];
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: Bearer ' . $accessToken->getAccessToken();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.strava.com/api/v3/activities/".$activityId."?" . http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            die();
        } else {
            return json_decode($response);
        }
    }

    /**
     * List Activity Comments
     * @param $accessToken
     * @param $activityId
     * @param null $page
     * @param null $per_page
     * @return mixed
     */
    public function getListActivity($accessToken, $activityId, $page = null, $per_page = null)
    {
        if($per_page === null) {$per_page = $this->activitiesPerPage;}

        $data = array(
            'page' => $page,
            'per_page' => $per_page
        );
        $curl = curl_init();
        $header = [];
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: Bearer ' . $accessToken->getAccessToken();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.strava.com/api/v3/activities/".$activityId."/comments?" . http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            die();
        } else {
            return json_decode($response);
        }
    }

    /**
     * List Activity Kudoers
     * @param $accessToken
     * @param $activityId
     * @param null $page
     * @param null $per_page
     * @return mixed
     */
    public function getListActivityKudoers($accessToken, $activityId, $page = null, $per_page = null)
    {
        if($per_page === null) {$per_page = $this->activitiesPerPage;}

        $data = array(
            'page' => $page,
            'per_page' => $per_page
        );
        $curl = curl_init();
        $header = [];
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: Bearer ' . $accessToken->getAccessToken();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.strava.com/api/v3/activities/".$activityId."/kudos?" . http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            die();
        } else {
            return json_decode($response);
        }
    }

    /**
     * List Activity Laps
     * @param $accessToken
     * @param $activityId
     * @return mixed
     */
    public function getListActivityLaps($accessToken, $activityId)
    {
        $curl = curl_init();
        $header = [];
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: Bearer ' . $accessToken->getAccessToken();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.strava.com/api/v3/activities/".$activityId."/laps",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            die();
        } else {
            return json_decode($response);
        }
    }

    /**
     * List Athlete Activities
     * @param $accessToken
     * @param null $before
     * @param null $after
     * @param null $page
     * @param null $per_page
     * @return array|mixed
     */
    public function getAthleteActivities($accessToken, $before = null, $after = null, $page = null, $per_page = null)
    {
        $data = array(
            'before' => $before,
            'after' => $after,
            'page' => $page,
            'per_page' => $per_page
        );

        $curl = curl_init();
        $header = [];
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: Bearer ' . $accessToken->getAccessToken();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.strava.com/api/v3/athlete/activities?" . http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            die();
        } else {
            return json_decode($response);
        }
    }

    /**
     * Get Activity Zones
     * @param $accessToken
     * @param $activityId
     * @return mixed
     */
    public function getActivityZones($accessToken, $activityId)
    {
        $curl = curl_init();
        $header = [];
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: Bearer ' . $accessToken->getAccessToken();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.strava.com/api/v3/activities/".$activityId."/zones",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            die();
        } else {
            return json_decode($response);
        }
    }


    //-------------------------------| Athletes |-------------------------------//

    /**
     * Get Authenticated Athlete
     * @param $accessToken
     * @return array|mixed
     */
    public function getAuthenticatedAthlete($accessToken)
    {
        $curl = curl_init();
        $header = [];
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: Bearer ' . $accessToken->getAccessToken();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.strava.com/api/v3/athlete",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            die();
        } else {
            return json_decode($response);
        }
    }

    /**
     * Get Zones
     * @param $accessToken
     * @return mixed
     */
    public function getZones($accessToken)
    {
        $curl = curl_init();
        $header = [];
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: Bearer ' . $accessToken->getAccessToken();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.strava.com/api/v3/athlete/zones",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            die();
        } else {
            return json_decode($response);
        }
    }

    /**
     * Get Athlete Stats
     * @param $accessToken
     * @return array|mixed
     */
    public function getAthleteStats($accessToken)
    {
        $curl = curl_init();
        $header = [];
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: Bearer ' . $accessToken->getAccessToken();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.strava.com/api/v3/athletes/".$this->athleteId."/stats",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            die();
        } else {
            return json_decode($response);
        }
    }

    //-------------------------------| Clubs |-------------------------------//

    /**
     * List Club Activities
     * @param $accessToken
     * @param $clubId
     * @param null $page
     * @param null $per_page
     * @return mixed
     */
    public function getListClubActivities($accessToken, $clubId, $page = null, $per_page = null)
    {
        if($per_page === null) {$per_page = $this->activitiesPerPage;}

        $data = array(
            'page' => $page,
            'per_page' => $per_page
        );
        $curl = curl_init();
        $header = [];
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: Bearer ' . $accessToken->getAccessToken();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.strava.com/api/v3/clubs/".$clubId."/activities?" . http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            die();
        } else {
            return json_decode($response);
        }
    }

    /**
     * List Club Administrators
     * @param $accessToken
     * @param $clubId
     * @param null $page
     * @param null $per_page
     * @return mixed
     */
    public function getListClubAdministrators($accessToken, $clubId, $page = null, $per_page = null)
    {
        if($per_page === null) {$per_page = $this->activitiesPerPage;}

        $data = array(
            'page' => $page,
            'per_page' => $per_page
        );
        $curl = curl_init();
        $header = [];
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: Bearer ' . $accessToken->getAccessToken();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.strava.com/api/v3/clubs/".$clubId."/admins?" . http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            die();
        } else {
            return json_decode($response);
        }
    }

    /**
     * Get Club
     * @param $accessToken
     * @param $clubId
     * @return mixed
     */
    public function getClub($accessToken, $clubId)
    {
        $curl = curl_init();
        $header = [];
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: Bearer ' . $accessToken->getAccessToken();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.strava.com/api/v3/clubs/".$clubId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            die();
        } else {
            return json_decode($response);
        }
    }

    /**
     * List Club Members
     * @param $accessToken
     * @param $clubId
     * @param null $page
     * @param null $per_page
     * @return mixed
     */
    public function getListClubMembers($accessToken, $clubId, $page = null, $per_page = null)
    {
        if($per_page === null) {$per_page = $this->activitiesPerPage;}

        $data = array(
            'page' => $page,
            'per_page' => $per_page
        );
        $curl = curl_init();
        $header = [];
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: Bearer ' . $accessToken->getAccessToken();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.strava.com/api/v3/clubs/".$clubId."/members?" . http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            die();
        } else {
            return json_decode($response);
        }
    }

    /**
     * List Athlete Clubs
     * @param $accessToken
     * @param null $page
     * @param null $per_page
     * @return mixed
     */
    public function getListAthleteClubs($accessToken, $page = null, $per_page = null)
    {
        if($per_page === null) {$per_page = $this->activitiesPerPage;}

        $data = array(
            'page' => $page,
            'per_page' => $per_page
        );
        $curl = curl_init();
        $header = [];
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: Bearer ' . $accessToken->getAccessToken();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.strava.com/api/v3/athlete/clubs?" . http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            die();
        } else {
            return json_decode($response);
        }
    }

    /**
     * Get all activites
     * @param $accessToken
     * @param null $before
     * @param null $after
     * @param null $page
     * @param null $per_page
     * @return array
     */
    public function getAllActivities($accessToken, $before = null, $after = null, $page = null, $per_page = null)
    {
        //Get athlete stats
        $atheleteStats = $this->getAthleteStats($accessToken);

        //Calculate total activities
        $totalActivities = $atheleteStats->all_ride_totals->count +
            $atheleteStats->all_run_totals->count +
            $atheleteStats->all_swim_totals->count;

        //Calaculate total pages based on total activities and activities per page
        $totalPages = ceil($totalActivities / $this->activitiesPerPage);
        //Loop trough all activies based on pages and activities per page and add them to array
        $allActivities = [];
        for ($page = 1; $page <= $totalPages; $page++) {
            $activities = $this->getAthleteActivities($accessToken, null, $after, $page, $this->activitiesPerPage);

            foreach ($activities AS $activity) {
                $activityInDb = $this->activityRepository->getItemByActivityId($activity->id);
                if (empty($activityInDb)) {
                    $allActivities[$activity->id] = $activity;
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
            return $this->setNewRounds($activityArr->splits_metric, $activity);
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
            return $this->setNewRounds($activityArr->splits_metric, $activity);
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

        $activity->setActivityId((int)$activityArr->id);
        $activity->setAthleteId($activityArr->athlete->id);
        $activity->setName($activityArr->name);
        $activity->setDistance($activityArr->distance);
        $activity->setMovingTime($activityArr->moving_time);
        $activity->setElapsedTime($activityArr->elapsed_time);
        $activity->setTotalElevationGain($activityArr->total_elevation_gain);
        $activity->setType($activityArr->type);
        $startDate = new \DateTime($activityArr->start_date);
        $activity->setStartDate($startDate);
        $startDateLocal = new \DateTime($activityArr->start_date_local);
        $activity->setStartDateLocal($startDateLocal);
        $activity->setTimezone($activityArr->timezone);
        $activity->setStartLat($activityArr->start_latlng[0]);
        $activity->setStartLng($activityArr->start_latlng[1]);
        $activity->setEndLat($activityArr->end_latlng[0]);
        $activity->setEndLng($activityArr->end_latlng[1]);
        $activity->setSummaryPolyline($activityArr->map->polyline);
        $activity->setAverageSpeed($activityArr->average_speed);
        $activity->setMaxSpeed($activityArr->max_speed);
        $activity->setAverageHeartrate($activityArr->average_heartrate);
        $activity->setMaxHeartrate($activityArr->max_heartrate);
        $activity->setElevHigh($activityArr->elev_high);
        $activity->setElevLow($activityArr->elev_low);
        $activity->setDescription($activityArr->description);
        $activity->setWorkoutType($activityArr->workout_type);

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
                $round->setDistance($item->distance);
                $round->setElapsedTime($item->elapsed_time);
                $round->setElevationDifference($item->elevation_difference);
                $round->setMovingTime($item->moving_time);
                $round->setSplit($item->split);
                $round->setAverageSpeed($item->average_speed);
                $round->setAverageHeartrate($item->average_heartrate);
                $round->setPaceZone($item->pace_zone);
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
