<?php

namespace Consolidate\Ticket\Event;

use Consolidate\Ticket\Data\Data;

class AddRole extends TicketEvent {
    public function getAction() {
        return "added role";
    }
}