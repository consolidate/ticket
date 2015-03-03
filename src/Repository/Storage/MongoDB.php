<?php

namespace Consolidate\Ticket\Repository\Storage;

use Consolidate\Ticket\Ticket;
use \MongoCollection;
use \MongoId;

class MongoDB implements Store
{
    protected $adapter;

    public function __construct(MongoCollection $adapter)
    {
        $this->adapter = $adapter;
    }

    public function load($id)
    {
        $data = $this->adapter->findOne(array('_id' => new MongoId($id)));
        $ticket = new Ticket($id);
        $ticket->fromArray($data);
        return $ticket;
    }

    public function persist(Ticket $ticket)
    {

    }
}