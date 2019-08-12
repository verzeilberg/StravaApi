<?php

namespace StravaApi\Service;

use StravaApi\Entity\ActivityImportLog;
use Zend\ServiceManager\ServiceLocatorInterface;


class StravaImportLogService implements StravaImportLogServiceInterface
{

    protected $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     *
     * Get import logs
     * @return      array
     *
     */
    public function getItems()
    {
        $importLogs = $this->entityManager->getRepository(ActivityImportLog::class)
            ->findBy([], ['importDate' => 'DESC']);

        return $importLogs;
    }

    /**
     *
     * Get import log
     * @return      object
     *
     */
    public function getLastItem()
    {
        $importLog = $this->entityManager->getRepository(ActivityImportLog::class)
            ->findOneBy([], ['importDate' => 'DESC']);

        return $importLog;
    }



    /**
     *
     * Set data to new activity
     *
     * @param       activity $activity object
     * @param       currentUser $currentUser whos is logged on
     * @return      void
     *
     */
    public function setNewImportLog($currentUser = null)
    {
        $importLog = $this->createImportLog();
;
        $importLog->setDateCreated(new \DateTime());
        $importLog->setCreatedBy($currentUser);
        $importLog->setImportDate(new \DateTime());

        $this->storeImportLog($importLog);
        return $importLog;
    }

    /**
     *
     * Get import log by  id
     * @var @id
     * @return      object
     *
     */
    public function getItemById($id)
    {
        $importLog = $this->entityManager->getRepository(ActivityImportLog::class)
            ->findOneBy(['id' => $id], []);

        return $importLog;
    }


    /**
     *
     * Save importlog to database
     *
     * @param       activity object
     * @return      void
     *
     */
    public function storeImportLog($importLog)
    {
        try {
            $this->entityManager->persist($importLog);
            $this->entityManager->flush();
            return true;
        } catch (\Doctrine\DBAL\DBALException $e) {
            return false;
        }
    }

    public function createImportLog()
    {
        return new ActivityImportLog();
    }
}
