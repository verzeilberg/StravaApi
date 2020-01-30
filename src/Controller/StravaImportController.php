<?php

namespace StravaApi\Controller;

use StravaApi\Repository\AccessTokenRepository;
use StravaApi\Service\StravaAccessTokenService;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\HelperPluginManager;
use StravaApi\Service\StravaService;
use StravaApi\Service\StravaOAuthService;
use Zend\View\Model\ViewModel;


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

    /**
     * @var StravaAccessTokenService
     */
    protected $accessTokenService;
    /*
     * @var Config
     */
    protected $config;

    public function __construct(
        HelperPluginManager $vhm,
        StravaService $stravaService,
        StravaOAuthService $stravaOAuthService,
        StravaAccessTokenService $accessTokenService,
        $config
    ) {
        $this->vhm = $vhm;
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
     * Index action to show all activities that can be imported into the db
     */
    public function indexAction()
    {

        $accessTokensExicst = $this->accessTokenService->accessTokenRepository->checkForAccessTokens();

        if (!$accessTokensExicst) {


            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost();

                $tokenData = $this->stravaOAuthService->tokenExchange($data->authorization_code);
                $result = $this->accessTokenService->createAccessToken($tokenData);
                if ($result) {
                    return $this->redirect()->toRoute('stravaimport');
                }

            }

            $code = $this->params()->fromQuery('code', null);
            if (!isset($code)) {
                $authorisationLink = $this->stravaOAuthService->getAuthorisationLink();
                $view = new ViewModel([
                    'authorisationLink' => $authorisationLink
                ]);
                $view->setTemplate('strava-api/strava-import/authorization');
            } else {
                $view = new ViewModel([
                    'code' => $code
                ]);
                $view->setTemplate('strava-api/strava-import/authorize');
            }
            return $view;
        }

        $accesToken = $this->accessTokenService->accessTokenRepository->getLatestAccessToken();
        $accessTokenVality = $this->accessTokenService->checkAccessTokenVality($accesToken);
        if (!$accessTokenVality) {
            $tokenData = $this->stravaOAuthService->refreshExchange($accesToken->getRefreshToken());
            $result = $this->accessTokenService->createAccessToken($tokenData);
            if ($result) {
                $accesToken = $this->accessTokenService->accessTokenRepository->getLatestAccessToken();
            }
        }

        //Get last import log
        $importLog = $this->stravaService->activityImportLogRepository->getLastItem();
        //Create a unix date from the date and time of last import log
        $importUnixDate = (is_object($importLog) && is_object($importLog->getImportDate()) ? $importLog->getImportDate()->format('U') : null);
        //Get all activities after given unix date
        $activities = $this->stravaService->getAllActivities($accesToken, null, $importUnixDate);

        //Return view
        return [
            'activities' => $activities,
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
        //Check if acces token exist
        $accessTokensExicst = $this->accessTokenService->accessTokenRepository->checkForAccessTokens();
        if (!$accessTokensExicst) {
            //Set error message
            $errorMessages = 'Er is nog geen access token!';
            //Set success boolean to false because of missing data
            $success = false;
            return new JsonModel([
                'errorMessages' => $errorMessages,
                'success' => $success
            ]);
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


        //Get activity id
        $activityId = (int)$this->params()->fromPost('activityId', null);
        //Get import id
        $importId = (int)$this->params()->fromPost('importId', null);

        //Check if code, activity id or import id is set
        if (empty($activityId) || empty($importId) || !isset($accesToken)) {
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

        //Get the Strava activity by client and activity id
        $activity = $this->stravaService->getActivity($accesToken, $activityId);
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
