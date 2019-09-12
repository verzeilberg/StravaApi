<?php

namespace StravaApi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use StravaApi\Repository\ActivityImportLogRepository;


class StravaLogController extends AbstractActionController
{

    /*
     * @var ViewHelperManager
     */
    protected $vhm;

    /*
     * @var
     */
    protected $repository;

    public function __construct(
        $vhm,
        ActivityImportLogRepository $repository

    )
    {
        $this->vhm = $vhm;
        $this->repository = $repository;
    }

    public function indexAction()
    {
        $this->layout('layout/beheer');

        $items = $this->repository->findBy([], ['importDate' => 'DESC']);

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
        $activityLog = $this->repository->findOneBy(['id' => $removelogId]);

        $item = $this->repository->findOneBy([], ['importDate' => 'DESC']);

        if (is_object($activityLog) && $item->getId() == $activityLog->getId()) {
            $this->repository->removeImportLog($activityLog);
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
