<?php

namespace AppBundle\EventListener;

use AppBundle\Event\ManifestEvent;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ManifestPullListener implements EventSubscriberInterface
{
    /**
     * @var ObjectManager
     */
    protected $om;

    public static function getSubscribedEvents()
    {
        return [
            'manifest.pull' => ['onManifestPull'],
        ];
    }

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function onManifestPull(ManifestEvent $event)
    {
        $this->om->getRepository('AppBundle:Manifest')->incrementPulls($event->getManifest());
    }
}
