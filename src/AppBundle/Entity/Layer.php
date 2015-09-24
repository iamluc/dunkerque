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
    const STATUS_PENDING = 1;
    const STATUS_PARTIAL = 2;
    const STATUS_COMPLETE = 3;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status = self::STATUS_PENDING;

    public function __construct($uuid = null)
    {
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

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return Layer
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
}
