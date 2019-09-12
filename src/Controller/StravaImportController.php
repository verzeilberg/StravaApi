<?php

namespace StravaApi\Controller;

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

    public function indexAction()
    {
        $this->layout('layout/beheer');
        $authoraisationLink = null;
        $activities = null;
        $code = $this->config['stravaSettings']['code'];
        if (empty($code)) {
            $code = $this->params()->fromQuery('code', null);
        }
        $importLog = $this->stravaService->activityImportLogRepository->getLastItem();
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
            $importLog = $this->stravaService->activityImportLogRepository->getItemById($importId);
            $success = $this->stravaService->setNewActivity($activity, $importLog, $this->currentUser());
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
        $importLog = $this->stravaService->activityImportLogRepository->setNewImportLog($this->currentUser());

        return new JsonModel([
            'errorMessages' => $errorMessages,
            'success' => $success,
            'importId' => $importLog->getId()
        ]);

    }


}
