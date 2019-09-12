<?php

namespace StravaApi\Controller;

use StravaApi\Service\StravaService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\HelperPluginManager;
use Zend\View\Model\JsonModel;


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

    public function indexAction()
    {
        $this->layout('layout/beheer');
        $items = $this->stravaService->activityImportLogRepository->findBy([], ['importDate' => 'DESC']);
        return [
            'items' => $items
        ];
    }

    public function removeImportAction()
    {
        $errorMessage = '';
        $success = true;
        $removelogId = (int)$this->params()->fromPost('removelogId', 0);
        if ($removelogId === NULL) {
            $errorMessage = 'Geen log id meegegeven!';
            $success = false;
        }
        $activityLog = $this->stravaService->activityImportLogRepository->findOneBy(['id' => $removelogId]);
        $item = $this->stravaService->activityImportLogRepository->findOneBy([], ['importDate' => 'DESC']);
        if (is_object($activityLog) && $item->getId() == $activityLog->getId()) {
            $this->stravaService->activityImportLogRepository->removeImportLog($activityLog);
        } else {
            $errorMessage = 'Geen log gevonden!';
            $success = false;
        }
        return new JsonModel([
            'errorMessage' => $errorMessage,
            'success' => $success,
            'removelogId' => $removelogId
        ]);
    }
}
