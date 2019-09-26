<?php

namespace StravaApi\Controller;

use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\HelperPluginManager;
use StravaApi\Service\StravaService;
use StravaApi\Service\StravaOAuthService;


class StravaImportController extends AbstractActionController
{

    /*
     * @var HelperPluginManager
     */
    protected $vhm;
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
        $config
    )
    {
        $this->vhm = $vhm;
        $this->stravaService = $stravaService;
        $this->stravaOAuthService = $stravaOAuthService;
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
     * Index action to show all activities that can be imported into the db
     */
    public function indexAction()
    {
        //Set authorisation link to null
        $authorisationLink = null;
        //Set activities to null
        $activities = null;
        //Get code for Strava client
        $code = $this->config['stravaSettings']['code'];
        //Check if code is set
        if (!isset($code)) {
            $code = $this->params()->fromQuery('code', null);
        }
        //Get last import log
        $importLog = $this->stravaService->activityImportLogRepository->getLastItem();
        //Create a unix date from the date and time of last import log
        $importUnixDate =  (is_object($importLog) && is_object($importLog->getImportDate())? $importLog->getImportDate()->format('U'):null);
        //Check if code is set. If not generate to get authorisation code
        if (!isset($code)) {
            //Get authorisation code
            $authorisationLink = $this->stravaOAuthService->getAuthorisationLink();
        } else {
            //Initialise the Strava client
            $this->stravaOAuthService->initialiseClient($code);
            //Get the client
            $client = $this->stravaOAuthService->getClient();
            //Get all activities after given unix date
            $activities = $this->stravaService->getAllActivities($client, null, $importUnixDate);
        }
        //Return view
        return [
            'authoraisationLink' => $authorisationLink,
            'activities' => $activities,
            'code' => $code,
            'importLog' => $importLog
        ];
    }

    /*
     * Add Strava activity to db
     */
    public function addactivityAction()
    {
        //Set error messages to null
        $errorMessages = '';
        //Set success boolean to true because so far so good
        $success = true;
        //Get code for Strava client
        $code = $this->config['stravaSettings']['code'];
        //Check if code is set
        if (!isset($code)) {
            $code = $this->params()->fromQuery('code', null);
        }
        //Get activity id
        $activityId = (int)$this->params()->fromPost('activityId', null);
        //Get import id
        $importId = (int)$this->params()->fromPost('importId', null);

        //Check if code, activity id or import id is set
        if (empty($activityId) || empty($importId) || !isset($code)) {
            //Set error message
            $errorMessages = 'Geen activity id/import id/code meegegeven!';
            //Set success boolean to false because of missing data
            $success = false;
            return new JsonModel([
                'errorMessages' => $errorMessages,
                'success' => $success,
                'activityId' => $activityId
            ]);
        }

        //Initialise the Strava client
        $this->stravaOAuthService->initialiseClient($code);
        //Get the client
        $client = $this->stravaOAuthService->getClient();
        //Get the Strava activity by client and activity id
        $activity = $this->stravaService->getActivity($client, $activityId);
        //Check if activity is set
        if (!empty($activity)) {
            //Get import log
            $importLog = $this->stravaService->activityImportLogRepository->getItemById($importId);
            //Add activity to db
            $success = $this->stravaService->newActivity($activity, $importLog, $this->currentUser());
        } else {
            //Set error message
            $errorMessages = 'Geen activity gevonden!';
            //Set success boolean to false because of missing activity
            $success = false;
        }

        return new JsonModel([
            'errorMessages' => $errorMessages,
            'success' => $success,
            'activityId' => $activityId
        ]);
    }


    /*
     * Create a import log
     */
    public function createimportlogAction()
    {
        //Set error messages to null
        $errorMessages = '';
        //Set success boolean to true because so far so good
        $success = true;
        //Create new import log send user so we know who created it
        $importLog = $this->stravaService->activityImportLogRepository->setNewImportLog($this->currentUser());

        $importId = null;
        //Check if a object is returned
        if (!is_object($importLog)) {
            $errorMessages = 'Er is iets mis gegaan bij het aanmaken van een import log!';
            $success = false;
        } else {
            //Set import id
            $importId = $importLog->getId();
        }

        //Return view
        return new JsonModel([
            'errorMessages' => $errorMessages,
            'success' => $success,
            'importId' => $importId
        ]);

    }


}
