<?php

namespace Consolidate\Ticket\Event;

use Consolidate\Ticket\Data\Data;

class SetStatus extends TicketEvent
{
    public static function getDataType()
    {
        return 'Consolidate\Ticket\Data\Status';
    }

    public function getAction()
    {
        return "set status";
    }
}
