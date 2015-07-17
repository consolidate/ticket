<?php

namespace Consolidate\Ticket\Importer;

use Consolidate\Ticket\Event\TicketEvent;

interface Resolver {
    
    /**
     * This method maps an event to a ticket it should belong to (or creates a
     * new ticket for it to be attached to).
     * 
     * @param  TicketEvent $event The event we need to find a ticket for
     * 
     * @return Ticket The ticket to attach the event to
     */
    public function getTicket(TicketEvent $event);
}