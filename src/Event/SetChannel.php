<?php

namespace Consolidate\Ticket\Event;

use Consolidate\Ticket\Data\Data;

class SetChannel extends TicketEvent
{
    public static function getDataType()
    {
        return 'Consolidate\Ticket\Data\Channel';
    }

    public function getAction()
    {
        return "set channel";
    }
}
