<?php

namespace AppBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class HeaderResponseListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'kernel.response' => ['onKernelResponse'],
        ];
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $event->getResponse()->headers->add([
            'Docker-Distribution-Api-Version' => 'registry/2.0',
        ]);
    }
}
