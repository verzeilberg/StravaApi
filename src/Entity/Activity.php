<?php

namespace StravaApi\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Model\UnityOfWork;

/**
 * This class represents a event item.
 * @ORM\Entity(repositoryClass="StravaApi\Repository\ActivityRepository")
 * @ORM\Table(name="activities")
 */
class Activity extends UnityOfWork
{

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="activity_id", type="string", length=265, nullable=false)
     * @Annotation\Options({
     * "label": "Athleet Id",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "readonly":"readonly"})
     */
    protected $activityId;


    /**
     * @ORM\Column(name="athlete_id", type="integer", length=11, nullable=false)
     * @Annotation\Options({
     * "label": "Athleet Id",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "readonly":"readonly"})
     */
    protected $athleteId;

    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     * @Annotation\Options({
     * "label": "Title",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "readonly":"readonly"})
     */
    protected $name;

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
     * @ORM\Column(name="moving_time", type="integer", length=11, nullable=false)
     * @Annotation\Options({
     * "label": "Moving time",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Moving time"})
     */
    protected $movingTime;

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
     * @ORM\Column(name="total_elevation_gain", type="float", nullable=false)
     * @Annotation\Options({
     * "label": "Total elevation gain",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Total elevation gain"})
     */
    protected $totalElevationGain;

    /**
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     * @Annotation\Options({
     * "label": "Type",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Type"})
     */
    protected $type;


    /**
     * @ORM\Column(name="workout_type", type="integer", length=1, nullable=true)
     * @Annotation\Options({
     * "label": "Workout type",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Workout type"})
     */
    protected $workoutType;


    /**
     * @ORM\Column(name="start_date", type="datetime", nullable=false)
     * @Annotation\Options({
     * "label": "Start date",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Start date"})
     */
    protected $startDate;

    /**
     * @ORM\Column(name="start_date_local", type="datetime", nullable=false)
     * @Annotation\Options({
     * "label": "Start date local",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Start date local"})
     */
    protected $startDateLocal;

    /**
     * @ORM\Column(name="timezone", type="string", length=255, nullable=false)
     * @Annotation\Options({
     * "label": "Timezone",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Timezone"})
     */
    protected $timezone;

    /**
     * @ORM\Column(name="start_lat", type="float", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Start latitude",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Start latitude"})
     */
    protected $startLat;

    /**
     * @ORM\Column(name="start_lng", type="float", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Start longitude",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Start longitude"})
     */
    protected $startLng;

    /**
     * @ORM\Column(name="end_lat", type="float", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "End latitude",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"End latitude"})
     */
    protected $endLat;

    /**
     * @ORM\Column(name="end_lng", type="float", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "End longitude",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"End longitude"})
     */
    protected $endLng;

    /**
     * @ORM\Column(name="summary_polyline", type="text", nullable=true)
     * @Annotation\Options({
     * "label": "End longitude",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"End longitude"})
     */
    protected $summaryPolyline;

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
     * @ORM\Column(name="max_speed", type="float", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Max speed",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Max speed"})
     */
    protected $maxSpeed;

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
     * @ORM\Column(name="max_heartrate", type="float", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Max heartrate",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Max heartrate"})
     */
    protected $maxHeartrate;

    /**
     * @ORM\Column(name="elev_high", type="float", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Elevation high",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Elevation high"})
     */
    protected $elevHigh;

    /**
     * @ORM\Column(name="elev_low", type="float", length=11, nullable=true)
     * @Annotation\Options({
     * "label": "Elevation low",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Elevation low"})
     */
    protected $elevLow;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Annotation\Options({
     * "label": "Description",
     * "label_attributes": {"class": "control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control"})
     */
    protected $description;

    /**
     * One activity has many rounds. This is the inverse side.
     * @ORM\OneToMany(targetEntity="Round", mappedBy="activity", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $rounds;


    /**
     * Many features have one product. This is the owning side.
     * @ORM\ManyToOne(targetEntity="ActivityImportLog", inversedBy="activities")
     * @ORM\JoinColumn(name="import_log_id", referencedColumnName="id")
     */
    private $activityImportLog;

    public function __construct()
    {
        $this->rounds = new ArrayCollection();
    }

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
    public function getActivityId()
    {
        return $this->activityId;
    }

    /**
     * @param mixed $activityId
     */
    public function setActivityId($activityId)
    {
        $this->activityId = $activityId;
    }

    /**
     * @return mixed
     */
    public function getAthleteId()
    {
        return $this->athleteId;
    }

    /**
     * @param mixed $athleteId
     */
    public function setAthleteId($athleteId)
    {
        $this->athleteId = $athleteId;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
    public function getTotalElevationGain()
    {
        return $this->totalElevationGain;
    }

    /**
     * @param mixed $totalElevationGain
     */
    public function setTotalElevationGain($totalElevationGain)
    {
        $this->totalElevationGain = $totalElevationGain;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getStartDateLocal()
    {
        return $this->startDateLocal;
    }

    /**
     * @param mixed $startDateLocal
     */
    public function setStartDateLocal($startDateLocal)
    {
        $this->startDateLocal = $startDateLocal;
    }

    /**
     * @return mixed
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param mixed $timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * @return mixed
     */
    public function getStartLat()
    {
        return $this->startLat;
    }

    /**
     * @param mixed $startLat
     */
    public function setStartLat($startLat)
    {
        $this->startLat = $startLat;
    }

    /**
     * @return mixed
     */
    public function getStartLng()
    {
        return $this->startLng;
    }

    /**
     * @param mixed $startLng
     */
    public function setStartLng($startLng)
    {
        $this->startLng = $startLng;
    }

    /**
     * @return mixed
     */
    public function getEndLat()
    {
        return $this->endLat;
    }

    /**
     * @param mixed $endLat
     */
    public function setEndLat($endLat)
    {
        $this->endLat = $endLat;
    }

    /**
     * @return mixed
     */
    public function getEndLng()
    {
        return $this->endLng;
    }

    /**
     * @param mixed $endLng
     */
    public function setEndLng($endLng)
    {
        $this->endLng = $endLng;
    }

    /**
     * @return mixed
     */
    public function getSummaryPolyline()
    {
        return $this->summaryPolyline;
    }

    /**
     * @param mixed $summaryPolyline
     */
    public function setSummaryPolyline($summaryPolyline)
    {
        $this->summaryPolyline = $summaryPolyline;
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
    public function getMaxSpeed()
    {
        return $this->maxSpeed;
    }

    /**
     * @param mixed $maxSpeed
     */
    public function setMaxSpeed($maxSpeed)
    {
        $this->maxSpeed = $maxSpeed;
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
    public function getMaxHeartrate()
    {
        return $this->maxHeartrate;
    }

    /**
     * @param mixed $maxHeartrate
     */
    public function setMaxHeartrate($maxHeartrate)
    {
        $this->maxHeartrate = $maxHeartrate;
    }

    /**
     * @return mixed
     */
    public function getElevHigh()
    {
        return $this->elevHigh;
    }

    /**
     * @param mixed $elevHigh
     */
    public function setElevHigh($elevHigh)
    {
        $this->elevHigh = $elevHigh;
    }

    /**
     * @return mixed
     */
    public function getElevLow()
    {
        return $this->elevLow;
    }

    /**
     * @param mixed $elevLow
     */
    public function setElevLow($elevLow)
    {
        $this->elevLow = $elevLow;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getActivityImportLog()
    {
        return $this->activityImportLog;
    }

    /**
     * @param mixed $activityImportLog
     */
    public function setActivityImportLog($activityImportLog)
    {
        $this->activityImportLog = $activityImportLog;
    }

    /**
     * @return mixed
     */
    public function getWorkoutType()
    {
        return $this->workoutType;
    }

    /**
     * @param mixed $workoutType
     */
    public function setWorkoutType($workoutType)
    {
        $this->workoutType = $workoutType;
    }

    /**
     * @return mixed
     */
    public function getRounds()
    {
        return $this->rounds;
    }

    /**
     * @param mixed $rounds
     */
    public function setRounds($rounds)
    {
        $this->rounds = $rounds;
    }


}
