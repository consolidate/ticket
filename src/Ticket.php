<?php

namespace Consolidate\Ticket;

use Consolidate\Ticket\Event\EventAware;
use Consolidate\Ticket\Event\TicketEvent;
use Consolidate\Ticket\Event\AddRole;
use Consolidate\Ticket\Event\RemoveRole;
use Consolidate\Ticket\Event\SetStatus;

use Consolidate\Ticket\Data\Role;
use Consolidate\Ticket\Data\Participant;
use Consolidate\Ticket\Data\Status;

use Illuminate\Support\Collection;
use \Exception;

class Ticket
{
    use EventAware;

    /**
     * All the events that have happened to this ticket
     * @var Collection
     */
    protected $timeline;

    protected $worker;

    protected $cached;

    public function __construct()
    {
        $this->timeline = new Collection();
    }

    public function getWithCaching($key, $callback)
    {
        if (empty($this->cached[$key])) {
            $this->cached[$key] = $callback();
        }

        return $this->cached[$key];
    }

    public function setDirty()
    {
        $this->cached = [];
    }

    public function addEvent(TicketEvent $event)
    {
        $this->timeline->push($event);

        // Set up some circle-referencing
        $event->setTicket($this);
        $event_name = 'ticket-' . str_replace(' ', '-', $event->getAction());
        $this->getEventManager()->dispatch($event_name, $event);
        $this->getEventManager()->dispatch('ticket-add-event', $event);

        $this->setDirty();
    }

    public function setStatus(Status $status)
    {
        $this->addEvent(new SetStatus($this->getWorker(), $status));
    }

    public function getStatus()
    {
        return $this->getWithCaching('status', function() {
            return $this->getTimeline()->reduce(function($status, $event) {
                if (get_class($event) == 'Consolidate\Ticket\Event\SetStatus') {
                    $status = $event->getData();
                }
                return $status;
            }, null);
        });
    }

    public function getParticipants()
    {
        return $this->getWithCaching('participants', function() {
            return array_unique($this->timeline->reduce(function($participants, $event) {
                $participants[] = $event->getWorker();
                return $participants;
            }, []));
        });
    }

    /**
     * Set the person who is currently modifying this ticket (not the same as
     * who the ticket assigned to). The worker is the Participant who is
     * modifying the ticket at this moment (adding an email, comment, etc)
     *
     * @param Participant $worker
     */
    public function setWorker(Participant $worker)
    {
        $this->worker = $worker;
    }

    public function getWorker()
    {
        return $this->worker;
    }

    public function assign(Participant $owner)
    {
        // First we must unassign anyone who this ticket is currently assigned to
        $roles = $this->getRoles();

        if (!empty($roles[Role::ASSIGNED])) {
            foreach ($roles[Role::ASSIGNED] as $participant) {
                $this->addEvent(new RemoveRole($this->getWorker(), new Role($participant, Role::ASSIGNED)));
            }
        }

        // Add our new assigned person
        $this->addEvent(new AddRole($this->getWorker(), new Role($owner, Role::ASSIGNED)));
    }

    /**
     * Get the participant who is currently assigned to this ticket
     *
     * @return Participant
     */
    public function getAssignedTo()
    {
        $roles = $this->getRoles();
        if (empty($roles[Role::ASSIGNED])) {
            throw new Exception('Ticket currently does not have an assigned worker');
        }
        return current($roles[Role::ASSIGNED]);
    }

    /**
     * Returns a list of roles on this ticket
     *
     * @return array key/value list of roles/participants
     */
    public function getRoles()
    {
        return $this->getWithCaching('roles', function() {
            return $this->getTimeline()->reduce(function($role, $event) {
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
        });
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
        return $this->getWithCaching('tags', function() {
            return $this->getData(['Consolidate\Ticket\Data\Tag'])->reduce(function($result, $event) {
                switch (get_class($event)) {
                    case 'Consolidate\Ticket\Event\AddTag':
                        $result[$event->getTag()] = $event->getTag();
                        break;
                    case 'Consolidate\Ticket\Event\RemoveTag':
                        unset($result[$event->getTag()]);
                        break;
                }
                return $result;
            });
        });
    }

    /**
     * Return a time ordered list of items attached to a ticket
     *
     * @return Collection
     */
    public function getTimeline()
    {
        return $this->timeline->sortBy(function($event) {
            return $event->getCreated();
        })->values();
    }

    public function getEvents(array $eventTypes)
    {
        return $this->getTimeline()->filter(function($event) use ($eventTypes) {
            return in_array(get_class($event), $eventTypes);
        });
    }

    public function getData(array $dataTypes)
    {
        return $this->getTimeline()->filter(function($event) use ($dataTypes) {
            return in_array(get_class($event->getData()), $dataTypes);
        });
    }
}
