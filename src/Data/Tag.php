<?php

namespace Consolidate\Ticket\Data;

class Tag implements Data
{
    protected $tag;

    public function __construct($tag)
    {
        $this->tag = $tag;
    }

    public function __toString()
    {
        return $this->tag;
    }
}
