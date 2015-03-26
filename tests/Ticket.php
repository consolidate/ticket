<?php

namespace Consolidate\Ticket\Tests;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

use Consolidate\Ticket\Ticket;

use Consolidate\Ticket\Data\Tag;
use Consolidate\Ticket\Data\Participant;
use Consolidate\Ticket\Data\Role;
use Consolidate\Ticket\Data\Comment;
use Consolidate\Ticket\Data\Status;
use Consolidate\Ticket\Data\Channel;

use Consolidate\Ticket\Event\TicketEvent;
use Consolidate\Ticket\Event\AddTag;
use Consolidate\Ticket\Event\RemoveTag;
use Consolidate\Ticket\Event\AddRole;
use Consolidate\Ticket\Event\RemoveRole;
use Consolidate\Ticket\Event\AddComment;
use Consolidate\Ticket\Event\SetStatus;
use Consolidate\Ticket\Event\SetChannel;


class TicketTest extends PHPUnit_Framework_TestCase
{
    private function _buildTicket() {
        $ticket = new Ticket();
        $participant = new Participant('bob@bob.com');
        $ticket->setWorker($participant);
        
        $time = strtotime('2015-02-23 21:11');

        $status = new SetStatus($participant, new Status(Status::OPEN), $time + 30);
        $ticket->addEvent($status);

        $event = new AddTag($participant, new Tag('Moo'), $time);
        $ticket->addEvent($event);

        $event = new AddTag($participant, new Tag('Moo2'), $time - 100);
        $ticket->addEvent($event);

        $event = new RemoveTag($participant, new Tag('Moo2'), $time - 50);
        $ticket->addEvent($event);

        $participant2 = new Participant('mike@mike.com');
        $event = new AddRole($participant, new Role($participant2, Role::OBSERVER), $time + 60);
        $ticket->addEvent($event);

        $event = new RemoveRole($participant, new Role($participant2, Role::OBSERVER), $time + 120);
        $ticket->addEvent($event);

        $comment = new AddComment($participant, new Comment('Long comment'), $time + 180);
        $ticket->addEvent($comment);

        return $ticket;
    }

    public function testAttemptWorkWithoutWorker() {
        $ticket = new Ticket();
        $this->setExpectedException('Exception', 'You cannot perform an action on this ticket until a worker is assigned.');
        $ticket->setStatus(new Status(Status::CLOSED));
    }

    public function testGetStatus() {
        $ticket = $this->_buildTicket();

        $this->assertEquals(Status::OPEN, $ticket->getStatus());

        $participant = new Participant('bob@bob.com');
        $time = strtotime('2015-02-23 21:11');
        $status = new SetStatus($participant, new Status(Status::CLOSED), $time + 60);
        $ticket->addEvent($status);

        $this->assertEquals(Status::CLOSED, (string)$ticket->getStatus());
        // Ensure that, once cached, we get the same result without all the work
        $this->assertEquals(Status::CLOSED, (string)$ticket->getStatus());

        $ticket->setWorker($participant);
        $ticket->setStatus(new Status(Status::QUEUED));
        $this->assertEquals(Status::QUEUED, (string)$ticket->getStatus());
    }

    public function testGetChannel() {
        $ticket = $this->_buildTicket();

        $this->assertEquals(Channel::UNKNOWN, (string)$ticket->getChannel());

        $ticket->setChannel(new Channel('Support'));
        $this->assertEquals('Support', (string)$ticket->getChannel());
    }

    public function testItemAdding() {
        $ticket = $this->_buildTicket();

        $this->assertCount(1, $ticket->getTags());

        $timeline = $ticket->getTimeline();
        $this->assertCount(7, $timeline);
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

    public function testGetAssignedUnknown() {
        //$this->setExpectedException('Exception', 'Ticket currently does not have an assigned worker');

        $ticket = new Ticket();
        $this->assertEquals('unassigned', $ticket->getAssignedTo());
    }

    public function testGetAssigned() {
        $ticket = new Ticket();
        $worker = new Participant('system');

        $ticket->setWorker($worker);
        $ticket->assign(new Participant('mike@mike.com'));
        $this->assertEquals('mike@mike.com', $ticket->getAssignedTo());

        $ticket->assign(new Participant('bob@bob.com'));
        $this->assertEquals('bob@bob.com', $ticket->getAssignedTo());
    }

    public function testGetEvents() {
        $ticket = $this->_buildTicket();

        $timeline = $ticket->getEvents(['Consolidate\Ticket\Event\AddTag']);
        $this->assertCount(2, $timeline);
        $this->assertEquals('bob@bob.com added tag Moo2 @ 2015-02-23 21:09', (string)$timeline[0]);
        $this->assertEquals('bob@bob.com added tag Moo @ 2015-02-23 21:11', (string)$timeline[1]);
    }

    public function testGetData() {
        $ticket = $this->_buildTicket();

        $timeline = $ticket->getData(['Consolidate\Ticket\Data\Tag']);
        $this->assertCount(3, $timeline);
        $this->assertEquals('bob@bob.com added tag Moo2 @ 2015-02-23 21:09', (string)$timeline[0]);
        $this->assertEquals('bob@bob.com removed tag Moo2 @ 2015-02-23 21:10', (string)$timeline[1]);
        $this->assertEquals('bob@bob.com added tag Moo @ 2015-02-23 21:11', (string)$timeline[2]);
    }

    public function testTimeline() {
        $ticket = $this->_buildTicket();

        $timeline = $ticket->getTimeline();
        $this->assertSame($ticket, $timeline[0]->getTicket());
        $this->assertEquals('bob@bob.com added tag Moo2 @ 2015-02-23 21:09', (string)$timeline[0]);
        $this->assertEquals('bob@bob.com removed tag Moo2 @ 2015-02-23 21:10', (string)$timeline[1]);
        $this->assertEquals('bob@bob.com added tag Moo @ 2015-02-23 21:11', (string)$timeline[2]);
        $this->assertEquals('bob@bob.com set status open @ 2015-02-23 21:11', (string)$timeline[3]);
        $this->assertEquals('bob@bob.com added role observer on mike@mike.com @ 2015-02-23 21:12', (string)$timeline[4]);
        $this->assertEquals('bob@bob.com removed role observer on mike@mike.com @ 2015-02-23 21:13', (string)$timeline[5]);
        $this->assertEquals('bob@bob.com added comment Long comment @ 2015-02-23 21:14', (string)$timeline[6]);
    }

    public function testGetCreated() {
        $ticket = $this->_buildTicket();
        $this->assertEquals(1424722160, $ticket->getCreated());
    }

    public function testIdAccessors() {
        $ticket = $this->_buildTicket();
        $this->assertEquals(0, $ticket->getId());
        $ticket->setId(10);
        $this->assertEquals(10, $ticket->getId());
    }

    public function testToArray() {
        $ticket = $this->_buildTicket();
        // Add the channel we left off in the basic build
        $ticket->setChannel(new Channel('Support'));

        $ticket2 = Ticket::fromArray($ticket->toArray());
        $this->assertEquals($ticket->toArray(), $ticket2->toArray());
    }
}
