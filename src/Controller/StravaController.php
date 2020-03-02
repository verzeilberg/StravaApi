<?php

namespace StravaApi\Controller;

use StravaApi\Service\StravaAccessTokenService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\HelperPluginManager;
use StravaApi\Service\StravaService;
use StravaApi\Service\StravaOAuthService;

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
     * @var StravaOAuthService
     */
    protected $stravaOAuthService;

    /*
     * @var Config
     */
    protected $config;


    public function __construct(
        HelperPluginManager $vhm,
        StravaService $stravaService,
        StravaOAuthService $stravaOAuthService,
        StravaAccessTokenService $accessTokenService,
        array $config
    )
    {
        $this->viewhelpermanager = $vhm;
        $this->stravaService = $stravaService;
        $this->stravaOAuthService = $stravaOAuthService;
        $this->accessTokenService = $accessTokenService;
        $this->config = $config;
    }

    /**
     * We override the parent class' onDispatch() method to
     * set an alternative layout for all actions in this controller.
     */
    public function onDispatch(MvcEvent $e)
    {
        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);

        // Set alternative layout
        $this->layout()->setTemplate('layout/beheer');

        // Return the response
        return $response;
    }

    /*
     * Index action to show a overview of the Strava accomplishments
     */
    public function indexAction()
    {
        //Set additional Stylesheets
        $this->viewhelpermanager->get('headLink')->appendStylesheet('/css/strava_style.css');
        $this->viewhelpermanager->get('headLink')->appendStylesheet('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css');
        //Set additional JavaScript
        $this->viewhelpermanager->get('headScript')->appendFile('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js');

        //Get strava data
        $totalRunActivities = $this->stravaService->activityRepository->getTotalItems('Run');
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
        //Get all activities
        $activities = $this->stravaService->activityRepository->getAllActivities();
        //Return view
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

    /*
     * Activiteiten action to show all activities
     */
    public function activiteitenAction()
    {

        $this->viewhelpermanager->get('headLink')->appendStylesheet('/css/timeshift/timeshift-1.0.css');
        $this->viewhelpermanager->get('headLink')->appendStylesheet('/css/timeshift/dateshift-1.0.css');
        $this->viewhelpermanager->get('headScript')->appendFile('/js/timeshift/timeshift-1.0.js');
        $this->viewhelpermanager->get('headScript')->appendFile('/js/timeshift/dateshift-1.0.js');

        //Get page from url for pagination
        $page = $this->params()->fromQuery('page', 1);

        $searchParams = null;
        if ($this->getRequest()->isPost())
        {
            $searchParams = $this->getRequest()->getPost();
            $query = $this->stravaService->activityRepository->filterActivities($searchParams);
        } else {
            //Get query for use in pagination
            $query = $this->stravaService->activityRepository->getActivities();

        }

        //Get activities for pagination
        $activities = $this->stravaService->getItemsForPagination($query, $page, 10);
        //Get years for year select
        $years = $this->stravaService->activityRepository->getYearsByActivities();
        //Return view
        return [
            'activities' => $activities,
            'years' => $years,
            'searchParams' => $searchParams
        ];
    }

    /*
     * Detail action to show details of a activity
     */
    public function detailAction()
    {
        //Set additional Stylesheets
        $this->viewhelpermanager->get('headLink')->appendStylesheet('/css/strava_style.css');
        $this->viewhelpermanager->get('headLink')->appendStylesheet('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css');
        //Set additional JavaScript
        $this->viewhelpermanager->get('headScript')->appendFile('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js');

        $authoraisationLink = null;
        //Get id from url
        $id = (int)$this->params()->fromRoute('id', null);
        //Check if id is empty
        if (empty($id)) {
            //Set flash message
            $this->flashMessenger()->addSuccessMessage('Geen id meegegeven');
            //Return to activities overview
            return $this->redirect()->toRoute('strava', ['action' => 'activiteiten']);
        }
        //Get activity based on id
        $activity = $this->stravaService->activityRepository->getActivityById($id);
        //If activity is empty
        if (empty($activity)) {
            //Set flash message
            $this->flashMessenger()->addSuccessMessage('Activiteit bestaat niet');
            //Return to activities overview
            return $this->redirect()->toRoute('strava', ['action' => 'activiteiten']);
        }
        //Get points from summary poly line to draw line on google maps
        $points = $this->stravaService->getPolyLinePoints($activity->getSummaryPolyline());
        //Get google maps key
        $googleMapKey = $this->config['stravaSettings']['googleMapKey'];
        //Return to view
        return [
            'activity' => $activity,
            'points' => $points,
            'rounds' => $activity->getRounds(),
            'googleMapKey' => $googleMapKey
        ];
    }


    /*
     * Update action to update activity in db with data from Strava
     */
    public function updateAction()
    {
        //Get id from url
        $id = (int)$this->params()->fromRoute('id', null);
        //Check if id is empty
        if (empty($id)) {
            //Set flash message
            $this->flashMessenger()->addSuccessMessage('Geen id meegegeven');
            //Return to activities overview
            return $this->redirect()->toRoute('strava', ['action' => 'activiteiten']);
        }
        //Get activity based on id
        $activity = $this->stravaService->activityRepository->getActivityById($id);
        //If activity is empty
        if (empty($activity)) {
            //Set flash message
            $this->flashMessenger()->addSuccessMessage('Activiteit bestaat niet');
            //Return to activities overview
            return $this->redirect()->toRoute('strava', ['action' => 'activiteiten']);
        }

        //Check if acces token exist
        $accessTokensExicst = $this->accessTokenService->accessTokenRepository->checkForAccessTokens();
        if (!$accessTokensExicst) {
            //Set flash message
            $this->flashMessenger()->addSuccessMessage('Er is nog geen acces token');
            //Return to activities overview
            return $this->redirect()->toRoute('strava', ['action' => 'activiteiten']);
        }

        //Get acces token en check vality
        $accesToken = $this->accessTokenService->accessTokenRepository->getLatestAccessToken();
        $accessTokenVality = $this->accessTokenService->checkAccessTokenVality($accesToken);
        if (!$accessTokenVality) {
            $tokenData = $this->stravaOAuthService->refreshExchange($accesToken->getRefreshToken());
            $result = $this->accessTokenService->createAccessToken($tokenData);
            if ($result) {
                $accesToken = $this->accessTokenService->accessTokenRepository->getLatestAccessToken();
            }
        }

        //Get the activity id known on strava
        $activityId = $activity->getActivityId();
        //Get the Strava activity by client and activity id
        $stravaActivity = $this->stravaService->getActivity($accesToken, $activityId);
        //Update the db with the activity from the Strava client
        $success = $this->stravaService->updateActivity($stravaActivity, $activity->getId(), $this->currentUser());
        //If success or not, return appropriate message
        if ($success) {
            $message = 'Activiteit geupdate!';
            $typeMessage = 'addSuccessMessage';
        } else {
            $message = 'Activiteit niet geupdate! Probeer het opnieuw!';
            $typeMessage = 'addErrorMessage';
        }
        //Set flash message
        $this->flashMessenger()->$typeMessage($message);
        //Return to activity
        return $this->redirect()->toRoute('strava', ['action' => 'detail', 'id' => $activity->getId()]);
    }

    public function addtimetodatabaseAction() {
        $activities = $this->stravaService->activityRepository->findAll();

        foreach ($activities as $activity) {
            $tempo = $activity->getAverageSpeed();
            $averageSpeed = gmdate("H:i:s",1000/$tempo);
            $averageSpeed = new \DateTime($averageSpeed);
            $activity->setAverageSpeedTime($averageSpeed);
            $this->stravaService->activityRepository->storeActivity($activity);
        }

    }
}
