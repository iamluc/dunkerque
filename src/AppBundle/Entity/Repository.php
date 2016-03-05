<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Repository.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="RepositoryRepository")
 */
class Repository
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var bool
     *
     * @ORM\Column(name="private", type="boolean")
     */
    private $private = true;

    /**
     * @var int
     *
     * @ORM\Column(name="stars", type="integer")
     */
    private $stars = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="pulls", type="integer")
     */
    private $pulls = 0;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Manifest", mappedBy="repository")
     */
    private $manifests;

    public function __construct($name = null, User $owner = null)
    {
        $this->name = $name;
        $this->owner = $owner;
        $this->manifests = new ArrayCollection();
        $this->repositoryStars = new ArrayCollection();
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
     * Set name.
     *
     * @param string $name
     *
     * @return Repository
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
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     *
     * @return Manifest
     */
    public function setOwner(User $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPrivate()
    {
        return $this->private;
    }

    /**
     * @return bool
     */
    public function isPublic()
    {
        return !$this->private;
    }

    /**
     * @param bool $private
     */
    public function setPrivate($private)
    {
        $this->private = (bool) $private;
    }

    /**
     * Set stars.
     *
     * @param int $stars
     *
     * @return Repository
     */
    public function setStars($stars)
    {
        $this->stars = $stars;

        return $this;
    }

    /**
     * Get stars.
     *
     * @return int
     */
    public function getStars()
    {
        return $this->stars;
    }

    /**
     * Set pulls.
     *
     * @param int $pulls
     *
     * @return Repository
     */
    public function setPulls($pulls)
    {
        $this->pulls = $pulls;

        return $this;
    }

    /**
     * Get pulls.
     *
     * @return int
     */
    public function getPulls()
    {
        return $this->pulls;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Repository
     */
    public function setTitle($title)
    {
        $this->title = $title;

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
     *
     * @return Repository
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getManifests()
    {
        return $this->manifests;
    }

    /**
     * @param mixed $manifests
     *
     * @return $this
     */
    public function setManifests($manifests)
    {
        $this->manifests = $manifests;

        return $this;
    }

    /**
     * @param Manifest $manifest
     *
     * @return $this
     */
    public function addManifest(Manifest $manifest)
    {
        $this->manifests[] = $manifest;

        return $this;
    }
}
