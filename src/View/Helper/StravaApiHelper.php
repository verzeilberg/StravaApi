<?php

namespace StravaApi\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Symfony\Component\VarDumper\VarDumper;
use function gmdate;
use function ltrim;
use function round;
use function str_replace;

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
        $minutes = floor((round($movingTime / 60)) % 60);
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
    public function getTotalElevationGain($totalElevationGain): string
    {
        return number_format($totalElevationGain, 0, ',', '');
    }

    /**
     * Get average speed in hours, minutes and seconds
     * @param $averageSpeed
     * @return bool|int|string
     */
    public function getAverageSpeed($averageSpeed): bool|int|string
    {
        if ($averageSpeed > 0) {

            $result = 1000 / (int)$averageSpeed;

            return gmdate("H:i:s", (int)$result);
        } else {
            return 0;
        }
    }

    /**
     * @param $averageSpeed
     * @return string
     */
    public function getAverageSpeedForChart($averageSpeed): string
    {
        $result = 1000/$averageSpeed;
        return ltrim(gmdate("i.s", (int) $result), 0);
    }

    /**
     * Get heartbeat
     *
     * @param $hearthBeat
     * @return string
     */
    public function getHeartbeath($hearthBeat): string
    {
        return number_format($hearthBeat, 0, ',', '');
    }

}
