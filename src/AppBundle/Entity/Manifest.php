<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Manifest.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ManifestRepository")
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
     * @var Repository
     *
     * @ORM\ManyToOne(targetEntity="Repository", cascade={"persist"})
     * @ORM\JoinColumn(name="repository_id", referencedColumnName="id", nullable=false)
     */
    private $repository;

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
     * Get tag.
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
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
        $this->digest = $this->computeDigest($content);

        $decoded = json_decode($content, true);
        $this->tag = $decoded['tag'];

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

    /**
     * Return the digest of the provided manifest.
     *
     * @param $manifest
     *
     * @return string
     */
    protected function computeDigest($manifest)
    {
        // See func `ParsePrettySignature` in https://github.com/docker/libtrust/blob/master/jsonsign.go
        $decoded = json_decode($manifest, true);
        $formatLength = null;
        $formatTail = null;
        foreach ($decoded['signatures'] as $signature) {
            $header = json_decode(base64_decode($signature['protected']), true);

            $formatLength = $header['formatLength'];
            $formatTail = $header['formatTail'];
        }

        // Manifest without signatures
        $manifest = substr($manifest, 0, $formatLength).base64_decode($formatTail);

        // Digest
        return 'sha256:'.hash('sha256', $manifest);
    }
}
