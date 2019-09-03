<?php

namespace StravaApi\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Model\UnityOfWork;

/**
 * This class represents a event item.
 * @ORM\Entity(repositoryClass="StravaApi\Repository\RoundRepository")
 * @ORM\Table(name="rounds")
 */
class Round extends UnityOfWork {

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="distance", type="float", nullable=false)
     * @Annotation\Options({
     * "label": "Afstand",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Afstand"})
     */
    protected $distance;

    /**
     * @ORM\Column(name="elapsed_time", type="integer", length=11, nullable=false)
     * @Annotation\Options({
     * "label": "Elapsed time",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Elapsed time"})
     */
    protected $elapsedTime;

    /**
     * @ORM\Column(name="elevation_difference", type="float", nullable=false)
     * @Annotation\Options({
     * "label": "Elevation difference",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Total elevation gain"})
     */
    protected $elevationDifference;

    /**
     * @ORM\Column(name="moving_time", type="integer", length=11, nullable=false)
     * @Annotation\Options({
     * "label": "Moving time",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Moving time"})
     */
    protected $movingTime;

    /**
     * @ORM\Column(name="split", type="integer", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Split",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Type"})
     */
    protected $split;

    /**
     * @ORM\Column(name="average_speed", type="float", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Average speed",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Average speed"})
     */
    protected $averageSpeed;

    /**
     * @ORM\Column(name="average_heartrate", type="float", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Average heartrate",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Average heartrate"})
     */
    protected $averageHeartrate;

    /**
     * @ORM\Column(name="pace_zone", type="integer", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Pace zone",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Max heartrate"})
     */
    protected $paceZone;

    /**
     * Many rounds have one activity. This is the owning side.
     * @ORM\ManyToOne(targetEntity="Activity", inversedBy="rounds")
     * @ORM\JoinColumn(name="activity_id", referencedColumnName="id")
     */
    private $activity;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @param mixed $distance
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;
    }

    /**
     * @return mixed
     */
    public function getElapsedTime()
    {
        return $this->elapsedTime;
    }

    /**
     * @param mixed $elapsedTime
     */
    public function setElapsedTime($elapsedTime)
    {
        $this->elapsedTime = $elapsedTime;
    }

    /**
     * @return mixed
     */
    public function getElevationDifference()
    {
        return $this->elevationDifference;
    }

    /**
     * @param mixed $elevationDifference
     */
    public function setElevationDifference($elevationDifference)
    {
        $this->elevationDifference = $elevationDifference;
    }

    /**
     * @return mixed
     */
    public function getMovingTime()
    {
        return $this->movingTime;
    }

    /**
     * @param mixed $movingTime
     */
    public function setMovingTime($movingTime)
    {
        $this->movingTime = $movingTime;
    }

    /**
     * @return mixed
     */
    public function getSplit()
    {
        return $this->split;
    }

    /**
     * @param mixed $split
     */
    public function setSplit($split)
    {
        $this->split = $split;
    }

    /**
     * @return mixed
     */
    public function getAverageSpeed()
    {
        return $this->averageSpeed;
    }

    /**
     * @param mixed $averageSpeed
     */
    public function setAverageSpeed($averageSpeed)
    {
        $this->averageSpeed = $averageSpeed;
    }

    /**
     * @return mixed
     */
    public function getAverageHeartrate()
    {
        return $this->averageHeartrate;
    }

    /**
     * @param mixed $averageHeartrate
     */
    public function setAverageHeartrate($averageHeartrate)
    {
        $this->averageHeartrate = $averageHeartrate;
    }

    /**
     * @return mixed
     */
    public function getPaceZone()
    {
        return $this->paceZone;
    }

    /**
     * @param mixed $paceZone
     */
    public function setPaceZone($paceZone)
    {
        $this->paceZone = $paceZone;
    }

    /**
     * @return mixed
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param mixed $activity
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;
    }


}
