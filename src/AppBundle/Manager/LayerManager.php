<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Layer;
use Doctrine\Common\Persistence\ObjectManager;
use League\Flysystem\Filesystem;

class LayerManager
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var Filesystem
     */
    private $fs;

    public function __construct(ObjectManager $om, Filesystem $fs)
    {
        $this->om = $om;
        $this->fs = $fs;
    }

    public function save(Layer $layer)
    {
        $this->om->persist($layer);
        $this->om->flush();
    }

    public function create()
    {
        return new Layer();
    }

    public function write(Layer $layer, $content)
    {
        $path = $this->getContentPath($layer);

        # Append content manually as Flysystem does not support it
        if ($this->fs->has($path)) {
            $stream = fopen('php://temp', 'w');
            stream_copy_to_stream($this->fs->readStream($path), $stream);
            stream_copy_to_stream($content, $stream);
        } else {
            $stream = $content;
        }

        $this->fs->putStream($path, $stream);
    }

    public function read(Layer $layer)
    {
        return $this->fs->readStream($this->getContentPath($layer));
    }

    public function getSize(Layer $layer)
    {
        return $this->fs->getSize($this->getContentPath($layer));
    }

    public function computeDigest(Layer $layer)
    {
        $hc = hash_init('sha256');
        hash_update_stream($hc, $this->fs->readStream($this->getContentPath($layer)));

        return 'sha256:'.hash_final($hc);
    }

    protected function getContentPath(Layer $layer)
    {
        return 'layers/'.$layer->getUuid();
    }
}
