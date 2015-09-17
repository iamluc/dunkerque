<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Manifest.
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Manifest
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
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=255)
     */
    private $tag;

    /**
     * @var string
     *
     * @ORM\Column(name="digest", type="string", length=255)
     */
    private $digest;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    public function __construct($name = null, $tag = null, $digest = null, $content = null)
    {
        $this->name = $name;
        $this->tag = $tag;
        $this->digest = $digest;
        $this->content = $content;
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
     * @return Manifest
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
     * Set tag.
     *
     * @param string $tag
     *
     * @return Manifest
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag.
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set digest.
     *
     * @param string $digest
     *
     * @return Manifest
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
     * Set content.
     *
     * @param string $content
     *
     * @return Manifest
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
