<?php

namespace Consolidate\Ticket\Event;

use Consolidate\Ticket\Data\Data;

class RemoveRole extends TicketEvent {
    public function getAction() {
        return "removed role";
    }
}