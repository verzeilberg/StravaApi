<?php

namespace StravaApi\Service;

interface StravaImportLogServiceInterface {

    /**
     *
     * Set data to new activity
     *
     * @param       activity $activity object
     * @param       currentUser $currentUser whos is logged on
     * @return      void
     *
     */
    public function setNewImportLog($currentUser = null);

}
