<?php

namespace Consolidate\Ticket\Repository\Storage;

use Consolidate\Ticket\Ticket;

use Aura\Sql\ExtendedPdo;

class MySQL implements Store
{
    protected $adapter;

    public function __construct(ExtendedPdo $adapter)
    {
        $this->adapter = $adapter;
    }

    public function load($id)
    {

    }

    public function persist(Ticket $ticket)
    {

    }
}