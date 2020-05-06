<?php

namespace StravaApi\Entity;

use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Model\UnityOfWork;

/**
 * This class represents a strava activity item.
 * @ORM\Entity(repositoryClass="StravaApi\Repository\AccessTokenRepository")
 * @ORM\Table(name="access_token")
 */
class AccessToken
{

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(name="expires_at", type="date", nullable=false)
     * @var date
     */
    protected $expiresAt;

    /**
     * @ORM\Column(name="expires_in", type="time", nullable=false)
     * @var time
     */
    protected $expiresIn;

    /**
     * @ORM\Column(name="refresh_token", type="string", length=255, nullable=false)
     * @var string
     */
    protected $refreshToken;

    /**
     * @ORM\Column(name="access_token", type="string", length=255, nullable=false)
     * @var string
     */
    protected $accessToken;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return AccessToken
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return timestamp
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param timestamp $expiresAt
     * @return AccessToken
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    /**
     * @return timestamp
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * @param timestamp $expiresIn
     * @return AccessToken
     */
    public function setExpiresIn($expiresIn)
    {
        $this->expiresIn = $expiresIn;
        return $this;
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     * @return AccessToken
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     * @return AccessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }


}
