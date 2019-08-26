<?php
namespace StravaApi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use StravaApi\Service\StravaDbService;

class StravaController extends AbstractActionController
{
    protected $viewhelpermanager;
    /*
     * @ StravaDbService
     */
    protected $stravaDbService;


    public function __construct($vhm, StravaDbService $stravaDbService) {
        $this->viewhelpermanager = $vhm;
        $this->stravaDbService = $stravaDbService;
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
        $longestActivity = $this->stravaDbService->getLongestActivity('Run');


        return [
            'totalRunActivities' => $totalRunActivities,
            'totalRunDistance' => $totalRunDistance,
            'totalRunTime' => $totalRunTime,
            'averageSpeed' => $averageSpeed,
            'averageElevation' => $averageElevation,
            'averageHeartbeat' => $averageHeartbeat,
            'fastestActivity' => $fastestActivity,
            'longestActivity' => $longestActivity
        ];
    }

    public function activiteitenAction()
    {
        $this->layout('layout/beheer');
        $page = $this->params()->fromQuery('page', 1);
        $query = $this->stravaDbService->getItems();

        $activities = $this->stravaDbService->getItemsForPagination($query, $page, 10);

        return [
            'activities' => $activities
        ];
    }

    public function detailAction()
    {

        $id = (int)$this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('strava');
        }


        return [
        ];
    }

}
