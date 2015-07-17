<?php

namespace Consolidate\Ticket\Event;

use Consolidate\Ticket\Data\Data;

class AddEmail extends TicketEvent
{
    public static function getDataType()
    {
        return 'Consolidate\Ticket\Data\Email';
    }

    public function getAction()
    {
        return "added email";
    }
}
