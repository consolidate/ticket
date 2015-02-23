<?php

namespace Consolidate\Ticket;

use Consolidate\Ticket\Event\TicketEvent;

use Illuminate\Support\Collection;

class Ticket {
    /**
     * All the events that have happened to this ticket
     * @var Collection
     */
    protected $timeline;

    /**
     * The roles of the participants engaged with this ticket
     * @var [type]
     */
    protected $role;

    public function __construct() {
        $this->timeline = new Collection();
        $this->role = [];
    }

    public function addEvent(TicketEvent $event) {
        $this->timeline->push($event);
    }

    public function getParticipants() {
        return array_unique($this->timeline->filter(function($event) {
            return $Event->getWorker();
        }));
    }

    /**
     * Play through the timeline and return which tags are currently applied,
     * taking into account those that have been added and those that have been
     * removed.
     * 
     * @return array List of tags on the ticket
     */
    public function getTags() {
        return $this->getTimeline()->reduce(function($result, $event) {
            if (get_class($event->getData()) == 'Consolidate\Ticket\Data\Tag') {
                switch (get_class($event)) {
                    case 'Consolidate\Ticket\Event\AddTagEvent':
                        $result[$event->getTag()] = $event->getTag();
                        break;
                    case 'Consolidate\Ticket\Event\RemoveTagEvent':
                        unset($result[$event->getTag()]);
                        break;
                }
            }
            return $result;
        });
    }

    /**
     * Return a time ordered list of items attached to a ticket
     * 
     * @return Collection
     */
    public function getTimeline() {
        return $this->timeline->sortBy(function($event) {
            return $event->getCreated();
        })->values();
    }
}