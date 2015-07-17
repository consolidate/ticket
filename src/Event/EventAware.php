<?php

namespace Consolidate\Ticket\Event;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Consolidate\Ticket\Event\Manager\Dummy;

trait EventAware
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventManager = null;

    /**
     * Return the event manager
     *
     * @return EventDispatcherInterface
     */
    public function getEventManager()
    {
        // If we haven't set an event manager, use a dummy for now
        if (empty($this->eventManager)) {
            $this->setEventManager(new Dummy());
        }
        return $this->eventManager;
    }

    /**
     * Set the event manager object
     */
    public function setEventManager(EventDispatcherInterface $eventManager)
    {
        $this->eventManager = $eventManager;
    }
}
