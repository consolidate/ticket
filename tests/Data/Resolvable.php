<?php

namespace Consolidate\Ticket\Tests\Data;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

use Consolidate\Ticket\Data\Participant;
use Consolidate\Ticket\Data\Resolvable;
use Consolidate\Ticket\Data\Store;

class ResolvableTest extends PHPUnit_Framework_TestCase
{
    public function testResolvable() {
        $participant = new Participant('bob@bob.com');
        $participant->setStore(new ParticipantStore());

        $this->assertEquals("bob@bob.com", (string)$participant);
        $this->assertFalse($participant->isResolved());
        $participant->resolve();
        $this->assertEquals("Bob McBobby", (string)$participant);
        $this->assertTrue($participant->isResolved());
    }

    public function testResolvableNotSupported() {
        $this->setExpectedException('Exception', 'Store does not support this data type.');

        $participant = new Participant('bob@bob.com');
        $participant->setStore(new UnsupportedStore());
    }
}


class ParticipantStore implements Store {
    public function supports($data) {
        return get_class($data) == "Consolidate\Ticket\Data\Participant";
    }

    public function resolve($data) {
        if ($data->getLabel() == "bob@bob.com") {
            $data->set("email", $data->getLabel());
            $data->set("name", "Bob McBobby");
            $data->setLabel("Bob McBobby");
            $data->setResolved(true);
        }
    }

    public function persist($data) {
        return true;
    }
}

class UnsupportedStore implements Store {
    public function supports($data) {
        return false;
    }

    public function resolve($data) {
        return true;
    }

    public function persist($data) {
        return true;
    }
}