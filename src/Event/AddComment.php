<?php

namespace Consolidate\Ticket\Event;

use Consolidate\Ticket\Data\Data;

class AddComment extends TicketEvent
{
    public static function getDataType()
    {
        return 'Consolidate\Ticket\Data\Comment';
    }

    public function getAction()
    {
        return "added comment";
    }
}
