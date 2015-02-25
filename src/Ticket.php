<?php

namespace Consolidate\Ticket;

use Consolidate\Ticket\Event\TicketEvent;
use Consolidate\Ticket\Event\AddRole;
use Consolidate\Ticket\Event\RemoveRole;
use Consolidate\Ticket\Data\Role;

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
        return array_unique($this->timeline->reduce(function($participants, $event) {
            $participants[] = $event->getWorker();
            return $participants;
        }, []));
    }

    public function assign(Participant $worker) {
        // First we must unassign anyone who this ticket is currently assigned to
        $roles = $this->getRoles();

        if (!empty($roles[Role::ASSIGNED])) {
            foreach ($roles[Role::ASSIGNED] as $participant) {
                $this->addEvent(new RemoveRole(Role::ASSIGNED, $participant));
            }
        }

        $this->addEvent(new AddRole(Role::ASSIGNED, $worker));
    }

    public function getAssignedTo() {
        $roles = $this->getRoles();
        return !empty($roles[Role::ASSIGNED]) ? current($roles[Role::ASSIGNED]) : false;
    }

    public function getRoles() {
        return $this->timeline->reduce(function($role, $event) {
            $role[Role::WORKER][(string)$event->getWorker()] = $event->getWorker();
            if (get_class($event->getData()) == 'Consolidate\Ticket\Data\Role') {
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
    public function getTags() {
        return $this->getTimeline()->reduce(function($result, $event) {
            if (get_class($event->getData()) == 'Consolidate\Ticket\Data\Tag') {
                switch (get_class($event)) {
                    case 'Consolidate\Ticket\Event\AddTag':
                        $result[$event->getTag()] = $event->getTag();
                        break;
                    case 'Consolidate\Ticket\Event\RemoveTag':
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