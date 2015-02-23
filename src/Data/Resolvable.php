<?php

namespace Consolidate\Ticket\Data;

/**
 * Describes the basic functionality of a resolvable data element
 */
trait Resolvable {
    protected $store;
    protected $is_resolved;

    public function setStore(Store $store) {
        $this->store = $store;
    }

    public function resolve() {
        return $this->store->resolve($this);
    }

    public function isResolved() {
        return $this->is_resolved;
    }
}