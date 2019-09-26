<?php

namespace StravaApi\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Model\UnityOfWork;

/**
 * This class represents a strava activity item.
 * @ORM\Entity(repositoryClass="StravaApi\Repository\ActivityRepository")
 * @ORM\Table(name="activities")
 */
class Activity extends UnityOfWork
{

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(name="activity_id", type="string", length=265, nullable=false)
     * @Annotation\Options({
     * "label": "Athleet Id",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "readonly":"readonly"})
     * @var string
     */
    protected $activityId;


    /**
     * @ORM\Column(name="athlete_id", type="integer", length=11, nullable=false)
     * @Annotation\Options({
     * "label": "Athleet Id",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "readonly":"readonly"})
     * @var integer
     */
    protected $athleteId;

    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     * @Annotation\Options({
     * "label": "Title",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "readonly":"readonly"})
     * @var string
     */
    protected $name;

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
     * @ORM\Column(name="total_elevation_gain", type="float", nullable=false)
     * @Annotation\Options({
     * "label": "Total elevation gain",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Total elevation gain"})
     * @var float
     */
    protected $totalElevationGain;

    /**
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     * @Annotation\Options({
     * "label": "Type",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Type"})
     * @var string
     */
    protected $type;


    /**
     * @ORM\Column(name="workout_type", type="integer", length=1, nullable=true)
     * @Annotation\Options({
     * "label": "Workout type",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Workout type"})
     * @var integer
     */
    protected $workoutType;


    /**
     * @ORM\Column(name="start_date", type="datetime", nullable=false)
     * @Annotation\Options({
     * "label": "Start date",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Start date"})
     * @var datetime
     */
    protected $startDate;

    /**
     * @ORM\Column(name="start_date_local", type="datetime", nullable=false)
     * @Annotation\Options({
     * "label": "Start date local",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Start date local"})
     * @var datetime
     */
    protected $startDateLocal;

    /**
     * @ORM\Column(name="timezone", type="string", length=255, nullable=false)
     * @Annotation\Options({
     * "label": "Timezone",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Timezone"})
     * @var string
     */
    protected $timezone;

    /**
     * @ORM\Column(name="start_lat", type="float", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Start latitude",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Start latitude"})
     * @var float
     */
    protected $startLat;

    /**
     * @ORM\Column(name="start_lng", type="float", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Start longitude",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Start longitude"})
     * @var float
     */
    protected $startLng;

    /**
     * @ORM\Column(name="end_lat", type="float", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "End latitude",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"End latitude"})
     * @var float
     */
    protected $endLat;

    /**
     * @ORM\Column(name="end_lng", type="float", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "End longitude",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"End longitude"})
     * @var float
     */
    protected $endLng;

    /**
     * @ORM\Column(name="summary_polyline", type="text", nullable=true)
     * @Annotation\Options({
     * "label": "End longitude",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"End longitude"})
     * @var string
     */
    protected $summaryPolyline;

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
     * @ORM\Column(name="max_speed", type="float", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Max speed",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Max speed"})
     * @var float
     */
    protected $maxSpeed;

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
     * @ORM\Column(name="max_heartrate", type="float", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Max heartrate",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Max heartrate"})
     * @var float
     */
    protected $maxHeartrate;

    /**
     * @ORM\Column(name="elev_high", type="float", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Elevation high",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Elevation high"})
     * @var float
     */
    protected $elevHigh;

    /**
     * @ORM\Column(name="elev_low", type="float", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Elevation low",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Elevation low"})
     * @var float
     */
    protected $elevLow;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Annotation\Options({
     * "label": "Description",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control"})
     * @var string
     */
    protected $description;

    /**
     * One activity has many rounds. This is the inverse side.
     * @ORM\OneToMany(targetEntity="Round", mappedBy="activity", orphanRemoval=true, cascade={"persist", "remove"})
     * @var object
     */
    private $rounds;


    /**
     * Many features have one product. This is the owning side.
     * @ORM\ManyToOne(targetEntity="ActivityImportLog", inversedBy="activities")
     * @ORM\JoinColumn(name="import_log_id", referencedColumnName="id")
     * @var object
     */
    private $activityImportLog;

    public function __construct()
    {
        $this->rounds = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Activity
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getActivityId()
    {
        return $this->activityId;
    }

    /**
     * @param string $activityId
     * @return Activity
     */
    public function setActivityId($activityId)
    {
        $this->activityId = $activityId;
        return $this;
    }

    /**
     * @return int
     */
    public function getAthleteId()
    {
        return $this->athleteId;
    }

    /**
     * @param int $athleteId
     * @return Activity
     */
    public function setAthleteId($athleteId)
    {
        $this->athleteId = $athleteId;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Activity
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @return Activity
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;
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
     * @return Activity
     */
    public function setMovingTime($movingTime)
    {
        $this->movingTime = $movingTime;
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
     * @return Activity
     */
    public function setElapsedTime($elapsedTime)
    {
        $this->elapsedTime = $elapsedTime;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotalElevationGain()
    {
        return $this->totalElevationGain;
    }

    /**
     * @param float $totalElevationGain
     * @return Activity
     */
    public function setTotalElevationGain($totalElevationGain)
    {
        $this->totalElevationGain = $totalElevationGain;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Activity
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getWorkoutType()
    {
        return $this->workoutType;
    }

    /**
     * @param int $workoutType
     * @return Activity
     */
    public function setWorkoutType($workoutType)
    {
        $this->workoutType = $workoutType;
        return $this;
    }

    /**
     * @return datetime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param datetime $startDate
     * @return Activity
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return datetime
     */
    public function getStartDateLocal()
    {
        return $this->startDateLocal;
    }

    /**
     * @param datetime $startDateLocal
     * @return Activity
     */
    public function setStartDateLocal($startDateLocal)
    {
        $this->startDateLocal = $startDateLocal;
        return $this;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     * @return Activity
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * @return float
     */
    public function getStartLat()
    {
        return $this->startLat;
    }

    /**
     * @param float $startLat
     * @return Activity
     */
    public function setStartLat($startLat)
    {
        $this->startLat = $startLat;
        return $this;
    }

    /**
     * @return float
     */
    public function getStartLng()
    {
        return $this->startLng;
    }

    /**
     * @param float $startLng
     * @return Activity
     */
    public function setStartLng($startLng)
    {
        $this->startLng = $startLng;
        return $this;
    }

    /**
     * @return float
     */
    public function getEndLat()
    {
        return $this->endLat;
    }

    /**
     * @param float $endLat
     * @return Activity
     */
    public function setEndLat($endLat)
    {
        $this->endLat = $endLat;
        return $this;
    }

    /**
     * @return float
     */
    public function getEndLng()
    {
        return $this->endLng;
    }

    /**
     * @param float $endLng
     * @return Activity
     */
    public function setEndLng($endLng)
    {
        $this->endLng = $endLng;
        return $this;
    }

    /**
     * @return string
     */
    public function getSummaryPolyline()
    {
        return $this->summaryPolyline;
    }

    /**
     * @param string $summaryPolyline
     * @return Activity
     */
    public function setSummaryPolyline($summaryPolyline)
    {
        $this->summaryPolyline = $summaryPolyline;
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
     * @return Activity
     */
    public function setAverageSpeed($averageSpeed)
    {
        $this->averageSpeed = $averageSpeed;
        return $this;
    }

    /**
     * @return float
     */
    public function getMaxSpeed()
    {
        return $this->maxSpeed;
    }

    /**
     * @param float $maxSpeed
     * @return Activity
     */
    public function setMaxSpeed($maxSpeed)
    {
        $this->maxSpeed = $maxSpeed;
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
     * @return Activity
     */
    public function setAverageHeartrate($averageHeartrate)
    {
        $this->averageHeartrate = $averageHeartrate;
        return $this;
    }

    /**
     * @return float
     */
    public function getMaxHeartrate()
    {
        return $this->maxHeartrate;
    }

    /**
     * @param float $maxHeartrate
     * @return Activity
     */
    public function setMaxHeartrate($maxHeartrate)
    {
        $this->maxHeartrate = $maxHeartrate;
        return $this;
    }

    /**
     * @return float
     */
    public function getElevHigh()
    {
        return $this->elevHigh;
    }

    /**
     * @param float $elevHigh
     * @return Activity
     */
    public function setElevHigh($elevHigh)
    {
        $this->elevHigh = $elevHigh;
        return $this;
    }

    /**
     * @return float
     */
    public function getElevLow()
    {
        return $this->elevLow;
    }

    /**
     * @param float $elevLow
     * @return Activity
     */
    public function setElevLow($elevLow)
    {
        $this->elevLow = $elevLow;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Activity
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return object
     */
    public function getRounds()
    {
        return $this->rounds;
    }

    /**
     * @param object $rounds
     * @return Activity
     */
    public function setRounds($rounds)
    {
        $this->rounds = $rounds;
        return $this;
    }

    /**
     * @return object
     */
    public function getActivityImportLog()
    {
        return $this->activityImportLog;
    }

    /**
     * @param object $activityImportLog
     * @return Activity
     */
    public function setActivityImportLog($activityImportLog)
    {
        $this->activityImportLog = $activityImportLog;
        return $this;
    }



}
