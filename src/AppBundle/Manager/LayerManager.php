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

    public function write(Layer $layer, $content, $append = false)
    {
        $path = $this->getContentPath($layer);
        if (!$this->fs->exists(dirname($path))) {
            $this->fs->mkdir(dirname($path));
        }

        $options = $append ? FILE_APPEND : null;

        return file_put_contents($path, $content, $options);
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
