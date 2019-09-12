<?php

namespace StravaApi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use StravaApi\Service\StravaDbService;
use StravaApi\Service\StravaService;
use StravaApi\Service\StravaOAuthService;
use Polyline;

class StravaController extends AbstractActionController
{
    protected $viewhelpermanager;
    /*
     * @ StravaDbService
     */
    protected $stravaDbService;

    /*
     * @ StravaService
     */
    protected $stravaService;

    /*
     * @var StravaOAuthService
     */
    protected $stravaOAuthService;

    /*
     * @var
     */
    protected $activityRepository;

    /*
 * @var Config
 */
    protected $config;


    public function __construct(
        $vhm,
        StravaDbService $stravaDbService,
        StravaService $stravaService,
        StravaOAuthService $stravaOAuthService,
        $activityRepository,
        $config
    )
    {
        $this->viewhelpermanager = $vhm;
        $this->stravaService = $stravaService;
        $this->stravaOAuthService = $stravaOAuthService;
        $this->stravaDbService = $stravaDbService;
        $this->activityRepository = $activityRepository;
        $this->config = $config;
    }

    public function indexAction()
    {
        $this->layout('layout/beheer');
        $this->viewhelpermanager->get('headLink')->appendStylesheet('/css/strava_style.css');

        $totalRunActivities = $this->stravaDbService->getTotalActivities('Run');
        $totalRunDistance = $this->stravaDbService->getTotalDistance('Run');
        $totalRunTime = $this->stravaDbService->getTotalTime('Run');
        $averageSpeed = $this->stravaDbService->getAverageSpeed('Run');
        $averageElevation = $this->stravaDbService->getAverageElevation('Run');
        $averageHeartbeat = $this->stravaDbService->getAverageHeartbeat('Run');

        $fastestActivity = $this->stravaDbService->getFastestActivity('Run');
        $fastestRound = $this->stravaDbService->getFastestRound('Run');
        $longestActivity = $this->stravaDbService->getLongestActivity('Run');

        $fastestTrainingActivity = $this->stravaDbService->getFastestActivity('Run', 3);
        $fastestTrainingRound = $this->stravaDbService->getFastestRound('Run', 3);
        $longestTrainingActivity = $this->stravaDbService->getLongestActivity('Run', 3);


        return [
            'totalRunActivities' => $totalRunActivities,
            'totalRunDistance' => $totalRunDistance,
            'totalRunTime' => $totalRunTime,
            'averageSpeed' => $averageSpeed,
            'averageElevation' => $averageElevation,
            'averageHeartbeat' => $averageHeartbeat,
            'fastestActivity' => $fastestActivity,
            'longestActivity' => $longestActivity,
            'fastestRound' => $fastestRound,
            'fastestTrainingActivity' => $fastestTrainingActivity,
            'fastestTrainingRound' => $fastestTrainingRound,
            'longestTrainingActivity' => $longestTrainingActivity
        ];
    }

    public function activiteitenAction()
    {
        $this->layout('layout/beheer');
        $page = $this->params()->fromQuery('page', 1);
        $query = $this->stravaDbService->getItems();

        $years = $this->stravaDbService->getYearsByActivities();
        $activities = $this->stravaDbService->getItemsForPagination($query, $page, 10);

        return [
            'activities' => $activities,
            'years' => $years
        ];
    }

    public function detailAction()
    {
        $this->layout('layout/beheer');
        $this->viewhelpermanager->get('headLink')->appendStylesheet('/css/strava_style.css');
        $this->viewhelpermanager->get('headLink')->appendStylesheet('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css');
        $this->viewhelpermanager->get('headScript')->appendFile('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js');
        $authoraisationLink = null;

        $id = (int)$this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('strava');
        }
        $activity = $this->stravaDbService->getActivityById($id);
        if (empty($activity)) {
            return $this->redirect()->toRoute('strava');
        }

        $encoded = $activity->getSummaryPolyline();
        $points = Polyline::decode($encoded);
        $points = Polyline::pair($points);

        $googleMapKey = $this->config['stravaSettings']['googleMapKey'];

        return [
            'activity' => $activity,
            'points' => $points,
            'rounds' => $activity->getRounds(),
            'googleMapKey' => $googleMapKey
        ];


    }



}
