<?php

namespace Consolidate\Ticket\Repository\Storage;


use Consolidate\Ticket\Repository\Storage\MongoDb\Filter;
use Consolidate\Ticket\Ticket;
use \MongoCollection;
use \MongoId;
use \Exception;

class MongoDb implements Store
{
    protected $adapter;

    public function __construct(MongoCollection $adapter)
    {
        $this->adapter = $adapter;
    }

    public function search(Filter $filter)
    {
        print_r($filter->toArray());
        $cursor = $this->adapter->find($filter->toArray(), ['_id' => 1]);
        $tickets = [];
        foreach ($cursor as $item) {
            $tickets[] = (string)$item['_id'];
        }

        return $tickets;
    }

    public function load($id)
    {
        $data = $this->adapter->findOne(['_id' => new MongoId($id)]);
        if (empty($data)) {
            throw new Exception('Invalid ID provided.');
        }
        $data['id'] = $data['_id'];
        return Ticket::fromArray($data);
    }

    public function persist(Ticket $ticket)
    {
        $data = $ticket->toArray();
        if ($ticket->getId()) {
            $data['_id'] = new MongoId($ticket->getId());
            unset($data['id']);
            $this->adapter->save($data);
        } else {
            $this->adapter->insert($data);
        }

        return (string)$data['_id'];
    }
}