<?php

namespace AppBundle\EventListener;

use AppBundle\Event\ManifestEvent;
use Doctrine\Common\Persistence\ObjectManager;
use Swarrot\Broker\Message;
use Swarrot\SwarrotBundle\Broker\Publisher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ManifestPushListener implements EventSubscriberInterface
{
    /**
     * @var ObjectManager
     */
    protected $om;

    /**
     * @var Publisher
     */
    private $publisher;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public static function getSubscribedEvents()
    {
        return [
            'manifest.push' => ['onManifestPush'],
        ];
    }

    public function __construct(ObjectManager $om, Publisher $publisher, SerializerInterface $serializer)
    {
        $this->om = $om;
        $this->publisher = $publisher;
        $this->serializer = $serializer;
    }

    public function onManifestPush(ManifestEvent $event)
    {
        $manifest = $event->getManifest();

        $message = new Message($this->serializer->serialize($manifest, 'json', ['groups' => ['manifest_push']]));
        $this->publisher->publish('manifest_push', $message);

        $manifest->setUpdatedAt(new \DateTime());
        $this->om->flush();
    }
}
