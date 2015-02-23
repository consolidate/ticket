<?php

namespace Consolidate\Ticket\Tests;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;


use Consolidate\Ticket\Ticket;

use Consolidate\Ticket\Data\Tag;
use Consolidate\Ticket\Data\Participant;
use Consolidate\Ticket\Event\TicketEvent;
use Consolidate\Ticket\Event\AddTagEvent;
use Consolidate\Ticket\Event\RemoveTagEvent;

class TicketTest extends PHPUnit_Framework_TestCase
{
    public function testItemAdding()
    {
        $ticket = new Ticket();

        $participant = new Participant('bob@bob.com');
        
        $time = strtotime('2015-02-23 21:11');
        $event = new AddTagEvent($participant, new Tag('Moo'), $time);
        $ticket->addEvent($event);

        $event = new AddTagEvent($participant, new Tag('Moo2'), $time - 100);
        $ticket->addEvent($event);

        $event = new RemoveTagEvent($participant, new Tag('Moo2'), $time - 50);
        $ticket->addEvent($event);

        $timeline = $ticket->getTimeline();
        $this->assertCount(3, $timeline);
        $this->assertEquals('Moo2', $timeline[0]->getTag());
        $this->assertEquals('Moo2', $timeline[1]->getTag());
        $this->assertEquals('Moo', $timeline[2]->getTag());

        $this->assertCount(1, $ticket->getTags());

        $this->assertEquals('bob@bob.com added tag Moo2 @ 2015-02-23 21:09', (string)$timeline[0]);
        $this->assertEquals('bob@bob.com removed tag Moo2 @ 2015-02-23 21:10', (string)$timeline[1]);
        $this->assertEquals('bob@bob.com added tag Moo @ 2015-02-23 21:11', (string)$timeline[2]);
    }
}
