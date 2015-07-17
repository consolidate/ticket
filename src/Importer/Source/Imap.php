<?php

namespace Consolidate\Ticket\Importer\Source;

use Consolidate\Ticket\Data\Email;
use Consolidate\Ticket\Data\Participant;
use Consolidate\Ticket\Event\AddEmail;
use Consolidate\Ticket\Event\EventAware;
use Consolidate\Ticket\Importer\Source\Email\TransportInterface;

use Symfony\Component\EventDispatcher\Event;


class Imap implements Source {
    use EventAware;

    const WORKER = 'email-parsed';

    protected $transport;

    public function __construct(TransportInterface $transport)
    {
        $this->transport = $transport;
    }

    /**
     * {@inheritdocs}
     */
    public function getEvents()
    {
        $mails = $this->transport->getUnseenMail();
        foreach ($mails as $mail) {
            $this->getEventManager()->dispatch('email-parsed', new Event($mail));

            $events[] = new AddEmail(
                new Participant(self::WORKER),
                $mail
            );
        }

        return $events;
    }
}