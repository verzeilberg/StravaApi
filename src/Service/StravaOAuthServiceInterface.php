<?php

namespace StravaApi\Service;

interface StravaOAuthServiceInterface {

    /**
     * Get authorisation link to oauth Strava client
     * @return mixed
     */
    public function getAuthorisationLink();

    /**
     * Initialise Strava client
     * @param $code code to initialise Strava client
     * @return bool|void
     */
    public function initialiseClient($code);

    /**
     * Get Strava client
     * @return mixed
     */
    public function getClient();

}
