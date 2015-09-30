<?php

namespace Consolidate\Ticket\Tests\Data;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

use Consolidate\Ticket\Event\TicketEvent;
use Consolidate\Ticket\Data\Participant;
use Consolidate\Ticket\Data\Tag;

use \LogicException;

class TicketEventTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorException()
    {
        $this->setExpectedException('LogicException', 'Created parameter expects a timestamp');

        $event = new TestEvent(new Participant('bob@bob.com'), new Tag('random_data'), 'this-is-not-valid');
    }

    public function testDataType()
    {
        $this->assertEquals('Consolidate\Ticket\Data\Data', TestEvent::getDataType());
    }
}

class TestEvent extends TicketEvent
{
    public function getAction()
    {
        return '';
    }
}