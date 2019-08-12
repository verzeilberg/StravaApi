<?php

namespace StravaApi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

use StravaApi\Service\StravaDbService;
use StravaApi\Service\StravaService;
use StravaApi\Service\StravaOAuthService;
use StravaApi\Service\StravaImportLogService;

class StravaImportController extends AbstractActionController
{

    /*
     * @var ViewHelperManager
     */
    protected $vhm;
    /*
     * @var StravaService
     */
    protected $stravaService;
    /*
     * @var stravaDbService
     */
    protected $stravaDbService;
    /*
     * @var StravaOAuthService
     */
    protected $stravaOAuthService;

    /*
     * @var StravaImportLogService
     */
    protected $stravaImportLogService;

    public function __construct(
        $vhm,
        StravaService $stravaService,
        StravaDbService $stravaDbService,
        StravaOAuthService $stravaOAuthService,
        StravaImportLogService $stravaImportLogService
    )
    {
        $this->vhm = $vhm;
        $this->stravaService = $stravaService;
        $this->stravaDbService = $stravaDbService;
        $this->stravaOAuthService = $stravaOAuthService;
        $this->stravaImportLogService = $stravaImportLogService;
    }

    public function indexAction()
    {
        $this->layout('layout/beheer');

        $authoraisationLink = null;
        $activities = null;
        $code = $this->params()->fromQuery('code', null);
        $code = '4c395e14916c858c1ba4dbd5ab288d69f64461a6';
        $importLog = $this->stravaImportLogService->getLastItem();
        $importUnixDate =  (is_object($importLog) && is_object($importLog->getImportDate())? $importLog->getImportDate()->format('U'):null);

        if (!isset($code)) {
            $authoraisationLink = $this->stravaOAuthService->getAuthorisationLink();
        } else {
            $this->stravaOAuthService->initialiseClient($code);
            $client = $this->stravaOAuthService->getClient();
            $activities = $this->stravaService->getAllActivities($client, null, $importUnixDate);
        }



        return [
            'authoraisationLink' => $authoraisationLink,
            'activities' => $activities,
            'code' => $code,
            'importLog' => $importLog
        ];
    }

    public function addactivityAction()
    {
        $errorMessages = '';
        $success = true;
        $code = $this->params()->fromPost('code', null);
        $activityId     = (int)$this->params()->fromPost('activityId', 0);
        $importId       = (int)$this->params()->fromPost('importId', 0);

        if ((empty($activityId) || $activityId < 0) || (empty($importId) || $importId < 0)) {
            $errorMessages = 'Geen activity id/import id meegegeven!';
            $success = false;
        }

        $this->stravaOAuthService->initialiseClient($code);
        $client = $this->stravaOAuthService->getClient();
        $activity = $this->stravaService->getActivity($client, $activityId);
        if (!empty($activity)) {
            $importLog = $this->stravaImportLogService->getItemById($importId);
            $success = $this->stravaDbService->setNewActivity($activity, $importLog, $this->currentUser());
        } else {
            $errorMessages = 'Geen activity gevonden!';
            $success = false;
        }

        return new JsonModel([
            'errorMessages' => $errorMessages,
            'success' => $success,
            'activityId' => $activityId
        ]);
    }

    public function createimportlogAction()
    {
        $success = true;
        $errorMessages = null;
        $importLog = $this->stravaImportLogService->setNewImportLog($this->currentUser());

        return new JsonModel([
            'errorMessages' => $errorMessages,
            'success' => $success,
            'importId' => $importLog->getId()
        ]);

    }


}
