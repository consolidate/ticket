<?php

namespace Consolidate\Ticket\Event;

use Consolidate\Ticket\Data\Data;

class TicketEvent {
    protected $created;
    protected $worker;

    /**
     * This is the type-specific data that belongs to the event. Each event
     * should implement its own class from this interface
     * @var Data
     */
    protected $data;

    public function __construct($worker, Data $data, $created = 0) {
        $this->worker = $worker;
        $this->data = $data;
        $this->created = $created ? $created : time();
    }

    public function getCreated() {
        return $this->created;
    }

    public function getWorker() {
        return $this->worker;
    }

    public function getData() {
        return $this->data;
    }
}