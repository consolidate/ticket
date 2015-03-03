<?php

namespace Consolidate\Ticket\Event;

use Consolidate\Ticket\Data\Data;

class AddRole extends TicketEvent
{
    public static function getDataType()
    {
        return 'Consolidate\Ticket\Data\Role';
    }

    public function getAction()
    {
        return "added role";
    }
}
