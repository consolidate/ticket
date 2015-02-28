<?php

namespace Consolidate\Ticket\Event;

use Consolidate\Ticket\Data\Data;

class SetStatus extends TicketEvent
{
    public function getAction()
    {
        return "set status";
    }
}
