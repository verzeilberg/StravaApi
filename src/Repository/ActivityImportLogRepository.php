<?php

namespace StravaApi\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use StravaApi\Entity\Activity;
use StravaApi\Entity\ActivityImportLog;

class ActivityImportLogRepository extends EntityRepository
{

    /**
     * Get all import logs ordered by import date descending
     * @return      array
     */
    public function getItems()
    {
        return $this->findBy([], ['importDate' => 'DESC']);
    }

    /**
     * Get last import log ordered by import date descending
     * @return      object
     */
    public function getLastItem()
    {
        return $this->findOneBy([], ['importDate' => 'DESC']);
    }

    /**
     * Set data to new activity
     * @param currentUser $currentUser who is logged on
     * @return      void
     * @throws OptimisticLockException
     */
    public function setNewImportLog($currentUser = null)
    {
        $importLog = $this->createImportLog();
        $importLog->setDateCreated(new \DateTime());
        $importLog->setCreatedBy($currentUser);
        $importLog->setImportDate(new \DateTime());
        $result = $this->storeImportLog($importLog);
        if(!$result) {
            return $result;
        }
        return $importLog;
    }

    /**
     * Get import log by id
     * @return      object
     * @var @id
     */
    public function getItemById($id)
    {
        return $this->findOneBy(['id' => $id], []);
    }

    /*
     * Create a new import log object
     * @return      object
     */
    public function createImportLog()
    {
        return new ActivityImportLog();
    }

    /**
     * Save import log to database
     * @param importLog object
     * @return      boolean
     * @throws OptimisticLockException
     */
    public function storeImportLog($importLog)
    {
        try {
            $this->getEntityManager()->persist($importLog);
            $this->getEntityManager()->flush();
            return true;
        } catch (\Doctrine\DBAL\DBALException $e) {
            return false;
        }
    }

    /**
     * Delete a import log and all activities objects from database
     * @param importLog $importLog object
     * @return bool
     * @throws OptimisticLockException
     */
    public function removeImportLog($importLog)
    {
        try {
            $this->getEntityManager()->remove($importLog);
            $this->getEntityManager()->flush();
            return true;
        } catch (\Doctrine\DBAL\DBALException $e) {
            return false;
        }
    }

}