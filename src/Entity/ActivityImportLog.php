<?php

namespace StravaApi\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Model\UnityOfWork;

/**
 * This class represents a event item.
 * @ORM\Entity(repositoryClass="StravaApi\Repository\ActivityImportLogRepository")
 * @ORM\Table(name="activity_import_log")
 */
class ActivityImportLog extends UnityOfWork {

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(name="import_date", type="datetime", nullable=false)
     * @var datetime
     */
    protected $importDate;

    /**
     * One activity import log has many activities. This is the inverse side.
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="activityImportLog", orphanRemoval=true, cascade={"persist", "remove"})
     * @var object
     */
    private $activities;

    public function __construct() {
        $this->activities = new ArrayCollection();
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
     * @return ActivityImportLog
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return datetime
     */
    public function getImportDate()
    {
        return $this->importDate;
    }

    /**
     * @param datetime $importDate
     * @return ActivityImportLog
     */
    public function setImportDate($importDate)
    {
        $this->importDate = $importDate;
        return $this;
    }

    /**
     * @return object
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * @param object $activities
     * @return ActivityImportLog
     */
    public function setActivities($activities)
    {
        $this->activities = $activities;
        return $this;
    }

}
