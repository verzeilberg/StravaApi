<?php

namespace StravaApi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\HelperPluginManager;
use StravaApi\Service\StravaService;
use Polyline;

class StravaController extends AbstractActionController
{
    /*
     * @var HelperPluginManager
     */
    protected $viewhelpermanager;

    /*
     * @var StravaService
     */
    protected $stravaService;

    /*
     * @var Config
     */
    protected $config;


    public function __construct(
        HelperPluginManager $vhm,
        StravaService $stravaService,
        array $config
    )
    {
        $this->viewhelpermanager = $vhm;
        $this->stravaService = $stravaService;
        $this->config = $config;
    }

    public function indexAction()
    {
        $this->layout('layout/beheer');
        $this->viewhelpermanager->get('headLink')->appendStylesheet('/css/strava_style.css');
        $this->viewhelpermanager->get('headLink')->appendStylesheet('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css');
        $this->viewhelpermanager->get('headScript')->appendFile('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js');
        //All data
        $totalRunActivities = $this->stravaService->activityRepository->getTotalActivities('Run');
        $totalRunDistance = $this->stravaService->activityRepository->getTotalDistance('Run');
        $totalRunTime = $this->stravaService->activityRepository->getTotalTime('Run');
        $averageSpeed = $this->stravaService->activityRepository->getAverageSpeed('Run');
        $averageElevation = $this->stravaService->activityRepository->getAverageElevation('Run');
        $averageHeartbeat = $this->stravaService->activityRepository->getAverageHeartbeat('Run');
        //Race data
        $fastestActivity = $this->stravaService->activityRepository->getFastestActivity('Run');
        $fastestRound = $this->stravaService->roundRepository->getFastestRound('Run');
        $longestActivity = $this->stravaService->activityRepository->getLongestActivity('Run');
        //Training data
        $fastestTrainingActivity = $this->stravaService->activityRepository->getFastestActivity('Run', 3);
        $fastestTrainingRound = $this->stravaService->roundRepository->getFastestRound('Run', 3);
        $longestTrainingActivity = $this->stravaService->activityRepository->getLongestActivity('Run', 3);

        $activities = $this->stravaService->activityRepository->getAllActivities();

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
            'longestTrainingActivity' => $longestTrainingActivity,
            'activities' => $activities
        ];
    }

    public function activiteitenAction()
    {
        $this->layout('layout/beheer');
        $page = $this->params()->fromQuery('page', 1);

        $query = $this->stravaService->activityRepository->getActivities();
        $activities = $this->stravaService->getItemsForPagination($query, $page, 10);
        $years = $this->stravaService->activityRepository->getYearsByActivities();
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
        $activity = $this->stravaService->activityRepository->getActivityById($id);
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
