<?php

namespace Consolidate\Ticket\Importer;

use Consolidate\Ticket\Ticket;
use Consolidate\Ticket\Event\EventAware;
use Consolidate\Ticket\Importer\Source\Source;
use Consolidate\Ticket\Importer\Resolver;


class Importer {
    use EventAware;

    protected $sources = [];
    protected $resolver = null;

    /**
     * Add a source to recieve events from.
     *
     * @param  Source $source The source we are adding to the list
     */
    public function addSource(Source $source)
    {
        $this->sources[] = $source;
    }

    /**
     * The import resolver is used to determine a mapping of an imported event
     * and the ticket it should be attached to or if it should create a new one
     *
     * @param  Resolver $resolver Set our resolver to determine if an imported event needs to be attached to a specific ticket
     */
    public function setResolver(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Process all our sources and, using the resulting events, determine which
     * tickets they need to be attached to.
     * 
     * @return array List of tickets with their new events added
     */
    public function process()
    {
        $tickets = [];
        foreach ($this->sources as $source) {
            $source->setEventManager($this->getEventManager());
            
            foreach ($source->getEvents() as $event) {
                if ($this->resolver) {
                    $ticket = $this->resolver->getTicket($event);
                } else {
                    $ticket = new Ticket();
                }

                $ticket->addEvent($event);

                $tickets[] = $ticket;
            }
        }

        return $tickets;
    }
}