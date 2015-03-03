<?php

namespace Consolidate\Ticket\Event;

use Consolidate\Ticket\Data\Data;

class RemoveTag extends TicketEvent
{
    public function getTag()
    {
        return (string)$this->getData();
    }

    public static function getDataType()
    {
        return 'Consolidate\Ticket\Data\Tag';
    }

    public function getAction()
    {
        return "removed tag";
    }
}
