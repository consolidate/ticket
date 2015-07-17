<?php

namespace Consolidate\Ticket\Tests;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

use Consolidate\Ticket\Data\Email;
use Consolidate\Ticket\Data\Participant;
use \ezcMailParser;
use \ezcMailFileSet;

use Consolidate\Ticket\Importer\Source\Email\TransportInterface;
use Consolidate\Ticket\Importer\Source\Imap;

class ImapTest extends PHPUnit_Framework_TestCase
{
    public function testImport() {
        $imap = new Imap(new testMailTransport('test', 'test', 'test'));
        $events = $imap->getEvents();
        
        $this->assertCount(1, $events);
        $this->assertEquals(Imap::WORKER, $events[0]->getWorker()->getLabel());
        $email = $events[0]->getData()->toArray();
        $this->assertEquals('redacted@redacted.com', (string)$email['to']);
        $this->assertEquals('php-fig@googlegroups.com', (string)$email['from']);
    }
}

class testMailTransport implements TransportInterface {

    /**
     * {@inheritdocs}
     */
    public function __construct($host, $username, $password, $folder = 'INBOX', $ssl = true)
    {
    }

    /**
     * {@inheritdocs}
     */
    public function getUnseenMail()
    {
        // Create a new mail parser object
        $parser = new ezcMailParser();
        // // Parse the set of messages retrieved from the server earlier
        $set = new ezcMailFileSet(["tests/Importer/MailingListRaw"]);

        // Wrap our result in a email interface class
        return array_map(function ($mail) {
            return new Email(
                $mail->generate(),
                new Participant($mail->from->email),
                new Participant($mail->headers['Delivered-To'][0])
            );
        }, $parser->parseMail($set));
    }
}
