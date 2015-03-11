<?php

namespace Consolidate\Ticket\Data;

class Channel implements Data
{
    const UNKNOWN = 'unknown';

    protected $channel;

    public function __construct($channel)
    {
        $this->channel = $channel;
    }

    public function toArray()
    {
        return [
            'channel' => $this->channel
        ];
    }

    public static function fromArray(array $data)
    {
        return new self($data['channel']);
    }

    public function __toString()
    {
        return $this->channel;
    }
}
