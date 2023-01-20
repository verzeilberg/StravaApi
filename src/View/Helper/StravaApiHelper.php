<?php

namespace StravaApi\View\Helper;

use Laminas\View\Helper\AbstractHelper;

// This view helper class translate text
class StravaApiHelper extends AbstractHelper
{

    /*
     * Get distance in kilometres
     *
     * @var $distance distance in metres
     *
     * return int
     */
    public function getDistance($distance)
    {
        return intval((($distance / 1000) *100))/100;
    }

    /*
     * Get moving time in hours, minutes and seconds
     *
     * @var $movingTime moving time in seconds
     *
     */
    public function getMovingTime($movingTime)
    {
        return gmdate("H:i:s", $movingTime);
    }

    /*
     * Get total moving time in hours, minutes and seconds
     *
     * @var $movingTime moving time in seconds
     *
     */
    public function getTotalMovingTime($movingTime)
    {
        $hours = floor($movingTime / 3600);
        $minutes = floor(($movingTime / 60) % 60);
        $seconds = $movingTime % 60;

        if(strlen($seconds) == 1) {
            $seconds = '0'.$seconds;
        }

        return "$hours:$minutes:$seconds";
    }

    /**
     * Get total elevation Gain
     * @param $totalElevationGain
     * @return string
     */
    public function getTotalElevationGain($totalElevationGain)
    {
        return number_format($totalElevationGain, 0, ',', '');
    }

    /**
     * Get average speed in hours, minutes and seconds
     * @param $averageSpeed
     * @return false|string
     */
    public function getAverageSpeed($averageSpeed)
    {
        return gmdate("H:i:s",1000/$averageSpeed);
    }

    /**
     * @param $averageSpeed
     * @return string
     */
    public function getAverageSpeedForChart($averageSpeed)
    {
        return ltrim(gmdate("i.s",1000/$averageSpeed), 0);
    }

    /**
     * Get heartbeat
     *
     * @param $hearthBeath
     * @return string
     */
    public function getHeartbeath($hearthBeath)
    {
        return number_format($hearthBeath, 0, ',', '');
    }

}
