<?php

namespace StravaApi\Controller;

use StravaApi\Service\StravaService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\View\HelperPluginManager;
use Laminas\View\Model\JsonModel;


class StravaLogController extends AbstractActionController
{

    /*
     * @var HelperPluginManager
     */
    protected $vhm;

    /*
     * @var StravaService
     */
    protected $stravaService;

    public function __construct(
        HelperPluginManager $vhm,
        StravaService $stravaService

    )
    {
        $this->vhm = $vhm;
        $this->stravaService = $stravaService;
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
        $this->vhm->get('headScript')->appendFile('/js/strava-log.js');

        // Return the response
        return $response;
    }

    /*
     * Get al import logs
     */
    public function indexAction()
    {
        //Get import log items
        $items = $this->stravaService->activityImportLogRepository->findBy([], ['importDate' => 'DESC']);
        //Return view
        return [
            'items' => $items
        ];
    }

    /*
     * Remove import action
     */
    public function removeImportAction()
    {
        //Set error messages to null
        $errorMessage = '';
        //Set success boolean to true because so far so good
        $success = true;
        //Get log id from post
        $logId = (int)$this->params()->fromPost('removelogId', null);
        //Check if log id is set
        if ($logId === null) {
            $errorMessage = 'Geen log id meegegeven!';
            $success = false;
            //Return json view
            return new JsonModel([
                'errorMessage' => $errorMessage,
                'success' => $success,
                'removelogId' => $logId
            ]);

        }
        //Get import log
        $importLog = $this->stravaService->activityImportLogRepository->findOneBy(['id' => $logId]);
        //Get the last import log
        $item = $this->stravaService->activityImportLogRepository->findOneBy([], ['importDate' => 'DESC']);
        //Check if given import log is same as the last import log.
        if (!is_object($importLog) && ($item->getId() != $importLog->getId())) {
            //Set error message
            $errorMessage = 'Geen log gevonden!';
            //Set success boolean to false because of missing data
            $success = false;
        } else {
            $result = $this->stravaService->activityImportLogRepository->removeImportLog($importLog);
            //Check if result is true if not something went wrong
            if (!$result) {
                //Set error message
                $errorMessage = 'Er is iets mis gegaan bij het verwijderen. Probeer opnieuw of neem contact op met uw systeem beheerder!';
                //Set success boolean to false because of missing data
                $success = false;
            }
        }
        //Return json view
        return new JsonModel([
            'errorMessage' => $errorMessage,
            'success' => $success,
            'removelogId' => $logId
        ]);
    }
}
