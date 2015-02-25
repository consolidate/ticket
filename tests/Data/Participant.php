<?php

namespace Consolidate\Ticket\Tests\Data;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

use Consolidate\Ticket\Data\Participant;

class ParticipantTest extends PHPUnit_Framework_TestCase
{
    public function testAccessors() {
        $participant = new Participant('bob@bob.com');

        $this->assertCount(0, $participant->toArray());

        $participant->fromArray([
            'moo'  => 1,
            'moo2' => 2
        ]);

        $this->assertCount(2, $participant->toArray());
        $this->assertEquals(1, $participant->get('moo'));
    }
}