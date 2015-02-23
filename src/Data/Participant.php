<?php

namespace Consolidate\Ticket\Data;

class Participant implements Data {
    use Resolvable;

    protected $key;

    public function __construct($key) {
        $this->key = $key;
    }

    public function getKey() {
        return $this->key;
    }

    public function __toString() {
        return $this->key;
    }
}