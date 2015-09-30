<?php

namespace Consolidate\Ticket\Data;

use \LogicException;
use \Exception;

/**
 * Describes the basic functionality of a resolvable data element
 */
trait Resolvable
{
    /**
     * The store this resolvable class should use to resolve
     * @var Consolidate\Ticket\Store
     */
    protected $store;

    /**
     * Has the resolver been called?
     * @var boolean
     */
    protected $is_resolved = false;

    /**
     * The unique identifier of the resolved entry
     * @var integer
     */
    protected $id;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        if (empty($this->id)) {
            throw new LogicException('No valid identification found for this participant.');
        }
        return $this->id;
    }

    public function setStore(Store $store)
    {
        if (!$store->supports($this)) {
            throw new Exception('Store does not support this data type.');
        }
        $this->store = $store;
    }

    public function setResolved($is_resolved)
    {
        $this->is_resolved = $is_resolved;
    }

    public function resolve()
    {
        $this->setId(0);
        return $this->store->resolve($this);
    }

    public function isResolved()
    {
        return $this->is_resolved;
    }
}
