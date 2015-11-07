<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * Webhook.
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class BuildConfig
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Groups({"repository_build"})
     */
    private $id;

    /**
     * @var Repository
     *
     * @ORM\ManyToOne(targetEntity="Repository", cascade={"persist"})
     * @ORM\JoinColumn(name="repository_id", referencedColumnName="id", nullable=false)
     *
     * @Serializer\Groups({"repository_build"})
     */
    private $repository;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255)
     *
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     *
     * @Serializer\Groups({"repository_build"})
     */
    private $reference;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=255)
     *
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     *
     * @Serializer\Groups({"repository_build"})
     */
    private $tag;

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
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     *
     * @return $this
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     *
     * @return $this
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }
}
