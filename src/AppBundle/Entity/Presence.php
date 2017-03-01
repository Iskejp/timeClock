<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne as ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn as JoinColumn;

/**
 * Presence
 *
 * @ORM\Table(name="presence")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PresenceRepository")
 */
class Presence
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timeIn", type="datetime")
     */
    private $timeIn;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timeOut", type="datetime", nullable=true)
     */
    private $timeOut;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timePeriod", type="datetime", nullable=true)
     */
    private $timePeriod;
    
    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="presences", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;    

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Presence
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Presence
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Set token
     *
     * @param string $token
     *
     * @return Presence
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }
    
    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set timeIn
     *
     * @param \DateTime $timeIn
     *
     * @return Presence
     */
    public function setTimeIn($timeIn)
    {
        $this->timeIn = $timeIn;

        return $this;
    }

    /**
     * Get timeIn
     *
     * @return \DateTime
     */
    public function getTimeIn()
    {
        return $this->timeIn;
    }

    /**
     * Set timeOut
     *
     * @param \DateTime $timeOut
     *
     * @return Presence
     */
    public function setTimeOut($timeOut)
    {
        $this->timeOut = $timeOut;

        return $this;
    }

    /**
     * Get timeOut
     *
     * @return \DateTime
     */
    public function getTimeOut()
    {
        return $this->timeOut;
    }
    
    /**
     * Set timePeriod
     *
     * @param \DateTime $timePeriod
     *
     * @return Presence
     */
    public function setTimePeriod($timePeriod)
    {
        $this->timePeriod = $timePeriod;

        return $this;
    }

    /**
     * Get timePeriod
     *
     * @return \Time
     */
    public function getTimePeriod()
    {
        return $this->timePeriod;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Presence
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
