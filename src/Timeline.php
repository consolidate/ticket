<?php

namespace Consolidate\Ticket;

use Illuminate\Support\Collection;

use Consolidate\Ticket\Data\Role;
use Consolidate\Ticket\Data\Participant;
use Consolidate\Ticket\Data\Status;
use Consolidate\Ticket\Data\Channel;

class Timeline extends Collection {

    /**
     * Return a time ordered list of items attached to a ticket
     *
     * @return Collection
     */
    public function compile()
    {
        return $this->sortBy(function($event) {
            return $event->getCreated();
        })->values();
    }

    /**
     * Return a timeline of specific events
     *
     * @param  array  $eventTypes What event types to return
     * @return Collection
     */
    public function getEvents(array $eventTypes)
    {
        return $this->compile()->filter(function($event) use ($eventTypes) {
            return in_array(get_class($event), $eventTypes);
        })->values();
    }

    /**
     * Return a timeline of specific data types
     *
     * @param  array  $dataTypes What data types should events contain
     * @return Collection
     */
    public function getData(array $dataTypes)
    {
        return $this->compile()->filter(function($event) use ($dataTypes) {
            return in_array(get_class($event->getData()), $dataTypes);
        })->values();
    }


    /**
     * Returns a list of roles on this ticket
     *
     * @return array key/value list of roles/participants
     */
    public function getRoles()
    {
        return $this->compile()->reduce(function($role, $event) {
            $role[Role::WORKER][(string)$event->getWorker()] = $event->getWorker();
            $data = $event->getData();
            switch (get_class($event)) {
                case 'Consolidate\Ticket\Event\AddRole':
                    $role[$data->getRole()][(string)$data->getParticipant()] = $data->getParticipant();
                    break;
                case 'Consolidate\Ticket\Event\RemoveRole':
                    unset($role[$data->getRole()][(string)$data->getParticipant()]);
                    if (empty($role[$data->getRole()])) {
                        unset($role[$data->getRole()]);
                    }
                    break;
            }
            return $role;
        }, []);
    }


    /**
     * Play through the timeline and return which tags are currently applied,
     * taking into account those that have been added and those that have been
     * removed.
     *
     * @return array List of tags on the ticket
     */
    public function getTags()
    {
        return array_values($this->getData(['Consolidate\Ticket\Data\Tag'])->reduce(function($result, $event) {
            switch (get_class($event)) {
                case 'Consolidate\Ticket\Event\AddTag':
                    $result[$event->getTag()] = $event->getTag();
                    break;
                case 'Consolidate\Ticket\Event\RemoveTag':
                    unset($result[$event->getTag()]);
                    break;
            }
            return $result;
        }, []));
    }

    public function toArray() {
        $result = [];
        foreach ($this->compile() as $event) {
            $result[] = $event->toArray();
        }
        return $result;
    }
}