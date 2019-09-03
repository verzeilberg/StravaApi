<?php

namespace StravaApi\View\Helper;

use Zend\View\Helper\AbstractHelper;

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

        if(count($seconds) == 1) {
            $seconds = '0'.$seconds;
        }

        return "$hours:$minutes:$seconds";
    }

    public function getTotalElevationGain($totalElevationGain)
    {
        return number_format($totalElevationGain, 0, ',', '');
    }

    /*
     * Get average speed in hours, minutes and seconds
     */
    public function getAverageSpeed($averageSpeed)
    {
        return gmdate("H:i:s",1000/$averageSpeed);
    }

    public function getAverageSpeedForChart($averageSpeed)
    {
        return ltrim(gmdate("i.s",1000/$averageSpeed), 0);
    }

    /*
 * Get hearthbeath
 */
    public function getHeartbeath($hearthBeath)
    {
        return number_format($hearthBeath, 0, ',', '');
    }

}