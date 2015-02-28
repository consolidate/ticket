<?php

namespace Consolidate\Ticket\Event\Manager;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

class Dummy implements EventDispatcherInterface
{
    /**
     * {@inheritdocs}
     */
    public function dispatch($eventName, Event $event = null)
    {
        return $event;
    }

    /**
     * {@inheritdocs}
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
    }

    /**
     * {@inheritdocs}
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
    }

    /**
     * {@inheritdocs}
     */
    public function removeListener($eventName, $listener)
    {
    }

    /**
     * {@inheritdocs}
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
    }

    /**
     * {@inheritdocs}
     */
    public function getListeners($eventName = null)
    {
        return [];
    }

    /**
     * {@inheritdocs}
     */
    public function hasListeners($eventName = null)
    {
        return false;
    }
}
