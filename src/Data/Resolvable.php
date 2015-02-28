<?php

namespace Consolidate\Ticket\Data;

use \Exception;

/**
 * Describes the basic functionality of a resolvable data element
 */
trait Resolvable
{
    protected $store;
    protected $is_resolved = false;

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
        return $this->store->resolve($this);
    }

    public function isResolved()
    {
        return $this->is_resolved;
    }
}
