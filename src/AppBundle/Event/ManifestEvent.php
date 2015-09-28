<?php

namespace AppBundle\Event;

use AppBundle\Entity\Manifest;
use Symfony\Component\EventDispatcher\Event;

class ManifestEvent extends Event
{
    /**
     * @var Manifest
     */
    protected $manifest;

    public function __construct(Manifest $manifest)
    {
        $this->manifest = $manifest;
    }

    public function getManifest()
    {
        return $this->manifest;
    }
}
