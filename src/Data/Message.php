<?php

namespace Consolidate\Ticket\Data;

use Consolidate\Ticket\Data\Participant;

class Message implements Data
{
    protected $raw;
    protected $from;
    protected $to;

    public function __construct($raw, Participant $from, Participant $to)
    {
        $this->raw = $raw;
        $this->from = $from;
        $this->to = $to;
    }

    public function toArray()
    {
        return [
            'raw'       => $this->raw,
            'from'      => $this->from,
            'to'        => $this->to
        ];
    }

    public static function fromArray(array $data)
    {
        return new self($data['raw'],
                        new Participant($data['from']),
                        new Participant($data['to']));
    }

    public function __toString()
    {
        return $this->raw;
    }
}
