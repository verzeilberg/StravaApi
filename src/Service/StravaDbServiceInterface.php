<?php

namespace StravaApi\Service;

interface StravaDbServiceInterface {

    /**
     * @param activities $activity
     * @return void
     *
     */
    public function loopTroughActivities($items);

}
