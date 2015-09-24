<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Layer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;

class LayerManager
{
    /**
     * @var ObjectManager
     */
    private $om;

    private $storagePath;
    private $fs;

    public function __construct(ObjectManager $om, $storagePath)
    {
        $this->om = $om;
        $this->storagePath = $storagePath;
        $this->fs = new Filesystem();
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
        if (!$this->fs->exists(dirname($path))) {
            $this->fs->mkdir(dirname($path));
        }

        $this->fs->dumpFile($path, $content);
    }

    public function computeDigest(Layer $layer)
    {
        return 'sha256:'.hash_file('sha256', $this->getContentPath($layer));
    }

    public function getContentPath(Layer $layer)
    {
        return $this->storagePath.'/layers/'.$layer->getUuid();
    }
}
