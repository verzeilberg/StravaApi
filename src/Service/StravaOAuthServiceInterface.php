<?php

namespace StravaApi\Service;

interface StravaOAuthServiceInterface {

    /**
     * @param activity $activity
     * @return boolean
     * 
     */
    public function initialiseClient($code);

}
