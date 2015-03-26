<?php

namespace Consolidate\Ticket\Tests\Repository;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;

use Consolidate\Ticket\Ticket;
use Consolidate\Ticket\Repository\Repository;
use Consolidate\Ticket\Repository\Storage\Store;

class RepositoryTest extends PHPUnit_Framework_TestCase
{
    public function testLoadAndPersist() {
        $repository = new Repository();
        $repository->setStorage(new FakeStore());
        
        $ticket = $repository->find(1);
        $this->assertEquals(1, $ticket->getId());
        $this->assertTrue($repository->save($ticket));
    }

    public function testMissingStore() {
        $this->setExpectedException('Exception', 'No storage engine specified.');

        $repository = new Repository();
        $repository->find(1);
    }
}


class FakeStore implements Store {
    public function load($id)
    {
        return new Ticket($id);
    }

    public function persist(Ticket $ticket)
    {
        return true;
    }
}