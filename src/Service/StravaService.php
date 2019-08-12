<?php

namespace StravaApi\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use StravaApi\Service\StravaDbService;
class StravaService implements StravaServiceInterface
{

    protected $athleteId;
    protected $activitiesPerPage;
    /*
     * @var stravaDbService
     */
    protected $stravaDbService;

    public function __construct($config, StravaDbService $stravaDbService)
    {
        $this->athleteId = $config['stravaSettings']['athleteId'];
        $this->activitiesPerPage = $config['stravaSettings']['activitiesPerPage'];
        $this->stravaDbService = $stravaDbService;
    }

    public function getAthlete($client)
    {
        return $client->getAthlete($this->athleteId);
    }

    public function getAthleteActivities($client, $before = null, $after = null, $page = null, $per_page = null)
    {
        return $client->getAthleteActivities($before, $after, $page, $per_page);
    }

    public function getAthleteStats($client)
    {
        return $client->getAthleteStats($this->athleteId);
    }

    public function getActivity($client, $activityId = null)
    {
        return $client->getActivity($activityId);
    }

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
            foreach($activities AS $activity)
            {
                $activityInDb = $this->stravaDbService->getItemByActivityId($activity["id"]);
                if(empty($activityInDb)) {
                    $allActivities[$activity["id"]] = $activity;
                }
            }
        }

        return $allActivities;

    }
}
