<?php

namespace Consolidate\Ticket\Data;

class Tag implements Data
{
    protected $tag;

    public function __construct($tag)
    {
        $this->tag = $tag;
    }

    public function toArray()
    {
        return [
            'tag' => $this->tag
        ];
    }

    public static function fromArray(array $data)
    {
        return new self($data['tag']);
    }

    public function __toString()
    {
        return $this->tag;
    }
}
