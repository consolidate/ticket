<?php

namespace Consolidate\Ticket\Event;

use Consolidate\Ticket\Ticket;
use Consolidate\Ticket\Data\Data;
use Consolidate\Ticket\Data\Participant;

use Symfony\Component\EventDispatcher\Event;
use \LogicException;

abstract class TicketEvent extends Event
{
    protected $created;
    protected $worker;
    protected $ticket;

    /**
     * This is the type-specific data that belongs to the event. Each event
     * should implement its own class from this interface
     * @var Data
     */
    protected $data;

    public function __construct(Participant $worker, Data $data, $created = 0)
    {
        $this->worker = $worker;
        $this->data = $data;
        if (!empty($created)) {
            if (!is_int($created)) {
                throw new LogicException("Created parameter expects a timestamp");
            }

            $this->created = $created;
        } else {
            $this->created = time();
        }
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getWorker()
    {
        return $this->worker;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setTicket(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function getTicket()
    {
        return $this->ticket;
    }

    public function toArray()
    {
        return [
            'class' => get_class($this),
            'event' => $this->getAction(),
            'data' => $this->getData()->toArray(),
            'worker' => $this->getWorker()->toArray(),
            'created' => $this->getCreated()
        ];
    }

    public static function fromArray(array $event)
    {
        $class = $event['class'];
        $data = $class::getDataType();

        return new $class(
            new Participant($event['worker']['label']),
            $data::fromArray($event['data']),
            $event['created']
        );
    }

    public abstract function getAction();

    public static function getDataType()
    {
        return 'Consolidate\Ticket\Data\Data';
    }

    public function __toString()
    {
        return (string)$this->getWorker() . " " . $this->getAction() . " " .
               (string)$this->getData() . " @ " . date("Y-m-d H:i", $this->getCreated());
    }
}
