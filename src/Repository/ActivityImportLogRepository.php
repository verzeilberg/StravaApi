<?php
namespace StravaApi\Repository;

use Doctrine\ORM\EntityRepository;
use StravaApi\Entity\ActivityImportLog;

class ActivityImportLogRepository extends EntityRepository
{

    /**
     *
     * Get import logs
     * @return      array
     *
     */
    public function getItems()
    {
        return $this->findBy([], ['importDate' => 'DESC']);
    }

    /*
     * Get import log
     * @return      object
     */
    public function getLastItem()
    {
        return $this->findOneBy([], ['importDate' => 'DESC']);
    }

    /*
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
        $importLog->setDateCreated(new \DateTime());
        $importLog->setCreatedBy($currentUser);
        $importLog->setImportDate(new \DateTime());
        $this->storeImportLog($importLog);
        return $importLog;
    }

    /**
     *
     * Get import log by id
     * @var @id
     * @return      object
     *
     */
    public function getItemById($id)
    {
        return $this->findOneBy(['id' => $id], []);
    }


    /**
     *
     * Save importlog to database
     *
     * @param       importLog object
     * @return      boolean
     *
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
     *
     * Delete a Import log and all activities objects from database
     * @param       importLog $importLog object
     * @return      void
     *
     */
    public function removeImportLog($importLog) {
        $this->getEntityManager()->remove($importLog);
        $this->getEntityManager()->flush();
    }

    /*
     * Create a new import log object
     * @return      object
     */
    public function createImportLog()
    {
        return new ActivityImportLog();
    }
}