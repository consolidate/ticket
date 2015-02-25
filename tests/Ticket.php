<?php

namespace Consolidate\Ticket\Tests;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;


use Consolidate\Ticket\Ticket;

use Consolidate\Ticket\Data\Tag;
use Consolidate\Ticket\Data\Participant;
use Consolidate\Ticket\Data\Role;

use Consolidate\Ticket\Event\TicketEvent;
use Consolidate\Ticket\Event\AddTag;
use Consolidate\Ticket\Event\RemoveTag;
use Consolidate\Ticket\Event\AddRole;
use Consolidate\Ticket\Event\RemoveRole;

class TicketTest extends PHPUnit_Framework_TestCase
{
    private function _buildTicket() {
        $ticket = new Ticket();
        $participant = new Participant('bob@bob.com');
        
        $time = strtotime('2015-02-23 21:11');
        $event = new AddTag($participant, new Tag('Moo'), $time);
        $ticket->addEvent($event);

        $event = new AddTag($participant, new Tag('Moo2'), $time - 100);
        $ticket->addEvent($event);

        $event = new RemoveTag($participant, new Tag('Moo2'), $time - 50);
        $ticket->addEvent($event);

        $participant2 = new Participant('mike@mike.com');
        $event = new AddRole($participant, new Role($participant2, Role::OBSERVER), $time + 1);
        $ticket->addEvent($event);

        $event = new RemoveRole($participant, new Role($participant2, Role::OBSERVER), $time + 3);
        $ticket->addEvent($event);

        return $ticket;
    }

    public function testItemAdding() {
        $ticket = $this->_buildTicket();

        $this->assertCount(1, $ticket->getTags());

        $timeline = $ticket->getTimeline();
        $this->assertCount(5, $timeline);
        $this->assertEquals('Moo2', $timeline[0]->getTag());
        $this->assertEquals('Moo2', $timeline[1]->getTag());
        $this->assertEquals('Moo', $timeline[2]->getTag());
    }

    public function testParticipants() {
        $ticket = $this->_buildTicket();
        $this->assertCount(1, $ticket->getParticipants());
    }

    public function testRoles() {
        $ticket = $this->_buildTicket();
        $roles = $ticket->getRoles();
        $worker1 = current($roles['worker']);
        $this->assertCount(1, $roles);
        $this->assertNotEmpty($roles['worker']);
        $this->assertEquals("bob@bob.com", (string)$worker1);

    }

    public function testTimeline() {
        $ticket = $this->_buildTicket();

        $timeline = $ticket->getTimeline();
        $this->assertEquals('bob@bob.com added tag Moo2 @ 2015-02-23 21:09', (string)$timeline[0]);
        $this->assertEquals('bob@bob.com removed tag Moo2 @ 2015-02-23 21:10', (string)$timeline[1]);
        $this->assertEquals('bob@bob.com added tag Moo @ 2015-02-23 21:11', (string)$timeline[2]);
        $this->assertEquals('bob@bob.com added role observer on mike@mike.com @ 2015-02-23 21:11', (string)$timeline[3]);
        $this->assertEquals('bob@bob.com removed role observer on mike@mike.com @ 2015-02-23 21:11', (string)$timeline[4]);
    }
}
