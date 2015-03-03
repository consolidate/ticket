<?php

namespace Consolidate\Ticket\Tests\Data;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

use Consolidate\Ticket\Data\Participant;

class ParticipantTest extends PHPUnit_Framework_TestCase
{
    public function testAccessors() {
        $participant = new Participant('bob@bob.com');

        $this->assertCount(1, $participant->toArray());
        $this->assertEquals('bob@bob.com', $participant->getLabel());

        $participant = Participant::fromArray([
            'label' => 'bob@bob.com',
            'moo'  => 1,
            'moo2' => 2
        ]);

        $this->assertCount(3, $participant->toArray());
        $this->assertEquals(1, $participant->get('moo'));
    }
}