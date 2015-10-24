<?php

namespace AppBundle\EventListener;

use AppBundle\Event\ManifestEvent;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ManifestPushListener implements EventSubscriberInterface
{
    /**
     * @var ObjectManager
     */
    protected $om;

    public static function getSubscribedEvents()
    {
        return [
            'manifest.push' => ['onManifestPush'],
        ];
    }

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function onManifestPush(ManifestEvent $event)
    {
        $event->getManifest()->setUpdatedAt(new \DateTime());
        $this->om->flush();
    }
}
