# ticket
Consolidate event driven ticket system


# Goal
To create a flexible and extensible event-driven ticketing system that is easy to integrate with existing data sources and systems.

The priorities are:

* Easy to configure
* Easy to extend
* Easy to debug


## Simple example

    use Consolidate\Ticket\Ticket;
    use Consolidate\Ticket\Event\TicketEvent;
    use Consolidate\Ticket\Data\Status;
    
    use Symfony\Component\EventDispatcher\EventDispatcher;
    
    $dispatcher = new EventDispatcher();
    $dispatcher->addListener('ticket-set-status', function (TicketEvent $event) {
        if ($event->getData() == 'Working On' && $event->getTicket()->getWorker() == 'Mike') {
            $ticket->addTag('PR Disaster');
            $ticket->addComment('This ticket has been touched my Mike. It is possible to salvage it if we act right now!');
        }
    });
    
    $ticket = new Ticket();
    $ticket->setEventManager($dispatcher);
    
    $ticket->setWorker(new Participant('Mike'));
    $ticket->setStatus(new Status('Working On'));
    $ticket->addComment('Told customer to shove it!');




## Import from sources

    $importer = new Importer(new SMTP());
    
    $dispatcher = new EventDispatcher();
    $dispatcher->addListener('importer-new-ticket', function (TicketEvent $event) {
        $ticket->setChannel(new Channel('Sales'));
    
        foreach ($ticket->getData(['Consolidate\Ticket\Data\Comment']) as $comment) {
            $language_detected = $language_detector->detect((string)$comment);
            $ticket->addTag($language_detected);
        }
    });