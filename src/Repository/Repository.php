<?php

namespace Consolidate\Ticket\Repository;

use Consolidate\Ticket\Repository\Storage\Store;
use Consolidate\Ticket\Ticket;

use \Exception;

class Repository
{
    protected $storage = null;

    public function setStorage(Store $storage)
    {
        $this->storage = $storage;
    }

    public function getStorage()
    {
        if (empty($this->storage)) {
            throw new Exception('No storage engine specified.');
        }

        return $this->storage;
    }

    public function persist(Ticket $ticket)
    {
        return $this->getStorage()->persist($ticket);
    }
}
