<?php

namespace Consolidate\Ticket\Repository;

use Consolidate\Ticket\Repository\Storage\Store;
use Consolidate\Ticket\Ticket;

use \Exception;

class Repository implements RepoInterface
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

    public function find($id, $findOrFail = false)
    {
        $storage = $this->getStorage();

        try {
            return $storage->load($id);
        } catch (Exception $exception) {
            if ($findOrFail) {
                throw $exception;
            }

            return new Ticket();
        }
    }

    public function save(Ticket $ticket)
    {
        return $this->getStorage()->persist($ticket);
    }

    public function delete(Ticket $ticket)
    {
        throw new Exception('Unsupported');
    }
}