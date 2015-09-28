<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class DelayedEvent extends Event
{
    /**
     * @var string
     */
    protected $trigger;

    /**
     * @var
     */
    private $eventName;

    /**
     * @var Event
     */
    protected $event;

    public function __construct($trigger, $eventName, Event $event)
    {
        $this->trigger = $trigger;
        $this->eventName = $eventName;
        $this->event = $event;
    }

    /**
     * @return string
     */
    public function getTrigger()
    {
        return $this->trigger;
    }

    /**
     * @return mixed
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }
}
