<?php

namespace Consolidate\Ticket\Event;

use Consolidate\Ticket\Data\Data;

class AddComment extends TicketEvent {
    public function getAction() {
        return "added comment";
    }
}