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
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(name="distance", type="float", nullable=false)
     * @Annotation\Options({
     * "label": "Afstand",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Afstand"})
     * @var float
     */
    protected $distance;

    /**
     * @ORM\Column(name="elapsed_time", type="integer", length=11, nullable=false)
     * @Annotation\Options({
     * "label": "Elapsed time",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Elapsed time"})
     * @var integer
     */
    protected $elapsedTime;

    /**
     * @ORM\Column(name="elevation_difference", type="float", nullable=false)
     * @Annotation\Options({
     * "label": "Elevation difference",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Total elevation gain"})
     * @var float
     */
    protected $elevationDifference;

    /**
     * @ORM\Column(name="moving_time", type="integer", length=11, nullable=false)
     * @Annotation\Options({
     * "label": "Moving time",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Moving time"})
     * @var integer
     */
    protected $movingTime;

    /**
     * @ORM\Column(name="split", type="integer", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Split",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Type"})
     * @var integer
     */
    protected $split;

    /**
     * @ORM\Column(name="average_speed", type="float", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Average speed",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Average speed"})
     * @var float
     */
    protected $averageSpeed;

    /**
     * @ORM\Column(name="average_heartrate", type="float", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Average heartrate",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Average heartrate"})
     * @var float
     */
    protected $averageHeartrate;

    /**
     * @ORM\Column(name="pace_zone", type="integer", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Pace zone",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Max heartrate"})
     * @var integer
     */
    protected $paceZone;

    /**
     * Many rounds have one activity. This is the owning side.
     * @ORM\ManyToOne(targetEntity="Activity", inversedBy="rounds")
     * @ORM\JoinColumn(name="activity_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * @var object
     */
    private $activity;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Round
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return float
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @param float $distance
     * @return Round
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;
        return $this;
    }

    /**
     * @return int
     */
    public function getElapsedTime()
    {
        return $this->elapsedTime;
    }

    /**
     * @param int $elapsedTime
     * @return Round
     */
    public function setElapsedTime($elapsedTime)
    {
        $this->elapsedTime = $elapsedTime;
        return $this;
    }

    /**
     * @return float
     */
    public function getElevationDifference()
    {
        return $this->elevationDifference;
    }

    /**
     * @param float $elevationDifference
     * @return Round
     */
    public function setElevationDifference($elevationDifference)
    {
        $this->elevationDifference = $elevationDifference;
        return $this;
    }

    /**
     * @return int
     */
    public function getMovingTime()
    {
        return $this->movingTime;
    }

    /**
     * @param int $movingTime
     * @return Round
     */
    public function setMovingTime($movingTime)
    {
        $this->movingTime = $movingTime;
        return $this;
    }

    /**
     * @return int
     */
    public function getSplit()
    {
        return $this->split;
    }

    /**
     * @param int $split
     * @return Round
     */
    public function setSplit($split)
    {
        $this->split = $split;
        return $this;
    }

    /**
     * @return float
     */
    public function getAverageSpeed()
    {
        return $this->averageSpeed;
    }

    /**
     * @param float $averageSpeed
     * @return Round
     */
    public function setAverageSpeed($averageSpeed)
    {
        $this->averageSpeed = $averageSpeed;
        return $this;
    }

    /**
     * @return float
     */
    public function getAverageHeartrate()
    {
        return $this->averageHeartrate;
    }

    /**
     * @param float $averageHeartrate
     * @return Round
     */
    public function setAverageHeartrate($averageHeartrate)
    {
        $this->averageHeartrate = $averageHeartrate;
        return $this;
    }

    /**
     * @return int
     */
    public function getPaceZone()
    {
        return $this->paceZone;
    }

    /**
     * @param int $paceZone
     * @return Round
     */
    public function setPaceZone($paceZone)
    {
        $this->paceZone = $paceZone;
        return $this;
    }

    /**
     * @return object
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param object $activity
     * @return Round
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;
        return $this;
    }

}
