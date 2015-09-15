<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rhumsaa\Uuid\Uuid;

/**
 * Layer.
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Layer
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * According to http://docs.docker.com/registry/spec/api/
     * "While the uuid parameter may be an actual UUID, this proposal imposes no constraints on the format and clients should never impose any.".
     *
     * @var guid
     *
     * @ORM\Column(name="uuid", type="string", length=255)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="digest", type="string", length=255, nullable=true)
     */
    private $digest;

    public function __construct($name, $uuid = null)
    {
        $this->name = $name;
        $this->uuid = $uuid ?: Uuid::uuid4()->toString();
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
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get uuid.
     *
     * @return guid
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set digest.
     *
     * @param string $digest
     *
     * @return Layer
     */
    public function setDigest($digest)
    {
        $this->digest = $digest;

        return $this;
    }

    /**
     * Get digest.
     *
     * @return string
     */
    public function getDigest()
    {
        return $this->digest;
    }
}
