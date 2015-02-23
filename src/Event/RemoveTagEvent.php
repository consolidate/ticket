<?php

namespace Consolidate\Ticket\Event;

use Consolidate\Ticket\Data\Data;

class RemoveTagEvent extends TicketEvent {
    public function getTag() {
        return (string)$this->getData();
    }

    public function getAction() {
        return "removed tag";
    }
}