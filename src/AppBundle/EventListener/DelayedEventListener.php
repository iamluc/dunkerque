<?php

namespace AppBundle\EventListener;

use AppBundle\Event\DelayedEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DelayedEventListener implements EventSubscriberInterface
{
    protected $events = [];

    public static function getSubscribedEvents()
    {
        return [
            'delayed' => ['onDelayedEvent']
        ];
    }

    public function onDelayedEvent(DelayedEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        if (!isset($this->events[$event->getTrigger()])) {
            $this->events[$event->getTrigger()] = [];
            $dispatcher->addListener($event->getTrigger(), [$this, 'triggerEvent']);
        }

        $this->events[$event->getTrigger()][] = $event;
    }

    public function triggerEvent(Event $triggerEvent, $eventName, EventDispatcherInterface $dispatcher)
    {
        /** @var DelayedEvent $delayedEvent */
        while ($delayedEvent = array_shift($this->events[$eventName])) {
            $dispatcher->dispatch($delayedEvent->getEventName(), $delayedEvent->getEvent());
        }
    }
}
