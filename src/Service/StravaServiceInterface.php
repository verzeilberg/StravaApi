<?php

namespace StravaApi\Service;

interface StravaServiceInterface {

    /**
     * @param activities $activity
     * @return array
     * 
     */
    public function getAthleteActivities($client);

}
