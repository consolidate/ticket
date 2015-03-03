<?php

namespace Consolidate\Ticket\Data;

class Comment implements Data
{
    protected $comment;

    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    public function toArray()
    {
        return [
            'comment' => $this->comment
        ];
    }

    public function __toString()
    {
        return $this->comment;
    }
}
