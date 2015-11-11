<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Webhook.
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Webhook
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
     * @var Repository
     *
     * @ORM\ManyToOne(targetEntity="Repository", cascade={"persist"})
     * @ORM\JoinColumn(name="repository_id", referencedColumnName="id", nullable=false)
     */
    private $repository;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     *
     * @Assert\NotBlank
     * @Assert\Length(max=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     *
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     * @Assert\Url
     */
    private $url;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_call", type="datetime", nullable=true)
     */
    private $lastCall;

    /**
     * @var string
     *
     * @ORM\Column(name="last_status", type="string", length=50, nullable=true)
     */
    private $lastStatus;

    public function __construct(Repository $repository = null)
    {
        $this->repository = $repository;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param Repository $repository
     *
     * @return Manifest
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Webhook
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return Webhook
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return \DateTime
     */
    public function getLastCall()
    {
        return $this->lastCall;
    }

    /**
     * @param \DateTime $lastCall
     *
     * @return $this
     */
    public function setLastCall(\DateTime $lastCall)
    {
        $this->lastCall = $lastCall;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastStatus()
    {
        return $this->lastStatus;
    }

    /**
     * @param string $lastStatus
     *
     * @return $this
     */
    public function setLastStatus($lastStatus)
    {
        $this->lastStatus = $lastStatus;

        return $this;
    }
}
