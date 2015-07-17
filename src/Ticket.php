<?php

namespace Consolidate\Ticket;

use Consolidate\Ticket\Event\EventAware;
use Consolidate\Ticket\Event\TicketEvent;
use Consolidate\Ticket\Event\AddRole;
use Consolidate\Ticket\Event\RemoveRole;
use Consolidate\Ticket\Event\SetStatus;
use Consolidate\Ticket\Event\SetChannel;

use Consolidate\Ticket\Data\Role;
use Consolidate\Ticket\Data\Participant;
use Consolidate\Ticket\Data\Status;
use Consolidate\Ticket\Data\Channel;

use Illuminate\Support\Collection;
use \Exception;

class Ticket
{
    use EventAware;

    protected $id;

    /**
     * All the events that have happened to this ticket
     *
     * @var Collection
     */
    protected $timeline;

    /**
     * Who is working on this ticket in this script instance.
     * NOT the same thing as "who is this ticket assigned to"
     *
     * @var Consolidate\Ticket\Data\Participant
     */
    protected $worker;

    /**
     * To reduce the cost of constantly walking the timeline, this variable
     * stores the results from various functions until a new event enters the
     * timeline (and the cache becomes dirty)
     *
     * @var array
     */
    protected $cached;

    public function __construct($id = 0)
    {
        $this->timeline = new Collection();
        $this->setDirty();
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Wrapper function around the caching system. If the cached entry is not
     * available, the callback is called to populate the cache
     *
     * @param  string   $key      The name of the cache key
     * @param  callable $callback The callback to execute to populate the cache
     * @return mixed
     */
    public function getWithCaching($key, callable $callback)
    {
        if (empty($this->cached[$key])) {
            $this->cached[$key] = $callback();
        }

        return $this->cached[$key];
    }

    /**
     * Mark the cache as dirty
     */
    public function setDirty()
    {
        $this->cached = [];
    }

    /**
     * Insert a new event into the timeline. This should make our cache dirty.
     *
     * @param TicketEvent $event The new event for our timeline
     */
    public function addEvent(TicketEvent $event)
    {
        $this->timeline->push($event);

        // Set up some circular-referencing
        $event->setTicket($this);
        // Fire off our events
        $event_name = 'ticket-' . str_replace(' ', '-', $event->getAction());
        $this->getEventManager()->dispatch($event_name, $event);
        $this->getEventManager()->dispatch('ticket-add-event', $event);

        $this->setDirty();
    }

    public function getCreated()
    {
        return $this->getTimeline()->shift()->getCreated();
    }

    /**
     * Change the status of the ticket as of now
     *
     * @param Status $status The new status
     */
    public function setStatus(Status $status)
    {
        $this->addEvent(new SetStatus($this->getWorker(), $status));
    }

    /**
     * Return the status of the ticket by picking the latest status change from
     * the timeline.
     *
     * @return Status
     */
    public function getStatus()
    {
        return $this->getWithCaching('status', function() {
            return $this->getEvents(['Consolidate\Ticket\Event\SetStatus'])->reduce(function($status, $event) {
                return $event->getData();
            }, null);
        });
    }

    /**
     * Change which channel a ticket belongs in (who should be dealing with this)
     *
     * @param Channel $channel The new channel
     */
    public function setChannel(Channel $channel)
    {
        $this->addEvent(new SetChannel($this->getWorker(), $channel));
    }

    /**
     * Return the channel of the ticket by picking the latest channel change from
     * the timeline.
     *
     * @return Status
     */
    public function getChannel()
    {
        return $this->getWithCaching('channel', function() {
            $channel = $this->getEvents(['Consolidate\Ticket\Event\SetChannel'])->reduce(function($channel, $event) {
                return $event->getData();
            }, null);

            return !empty($channel) ? $channel : new Channel(Channel::UNKNOWN);
        });
    }

    /**
     * Get all the participants who have contributed to this ticket by
     * examining each event in the timeline for the person who created it.
     *
     * @return Collection
     */
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

    /**
     * Return the current modifier of this ticket for this instance
     *
     * @return Participant
     */
    public function getWorker()
    {
        if (empty($this->worker)) {
            throw new Exception('You cannot perform an action on this ticket until a worker is assigned.');
        }
        return $this->worker;
    }

    /**
     * Assign this ticket to a particular participant
     *
     * @param  Participant $owner Who to assign this ticket to
     */
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
            return 'unassigned';
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
            }));
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

    /**
     * Return a timeline of specific events
     *
     * @param  array  $eventTypes What event types to return
     * @return Collection
     */
    public function getEvents(array $eventTypes)
    {
        return $this->getTimeline()->filter(function($event) use ($eventTypes) {
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
        return $this->getTimeline()->filter(function($event) use ($dataTypes) {
            return in_array(get_class($event->getData()), $dataTypes);
        })->values();
    }

    /**
     * Provide a lossless cachable/storable array of the configuration of the
     * ticket and it's timeline
     *
     * @return array
     */
    public function toArray()
    {
        // We want to flatten certain values for quicker searching from store
        $participants = array_map(
            function ($item) {
                return $item->toArray();
            },
            $this->getParticipants()
        );
  
        $roles = [];
        foreach ($this->getRoles() as $key => $role) {
            $roles[$key] = array_map(
                function ($item) {
                    return $item->toArray();
                },
                array_values($role)
            );
        }

        return [
            'id'           => $this->id,
            'status'       => (string)$this->getStatus(),
            'channel'      => (string)$this->getChannel(),
            'assigned_to'  => (string)$this->getAssignedTo(),
            'participants' => $participants,
            'roles'        => $roles,
            'tags'         => $this->getTags(),
            'timeline'     => $this->getTimeline()->map(function($event) {
                return $event->toArray();
            })->toArray()
        ];
    }

    public static function fromArray(array $data)
    {
        $ticket = new Ticket($data['id']);
        $participants = [];

        foreach ($data['timeline'] as $event) {
            $ticket->addEvent(TicketEvent::fromArray($event));
        }

        return $ticket;
    }
}
