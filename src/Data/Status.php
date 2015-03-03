<?php

namespace Consolidate\Ticket\Data;

class Status implements Data
{
    const OPEN        = 'open';
    const QUEUED      = 'queued';
    const ASSIGNED    = 'assigned';
    const IN_PROGRESS = 'in progress';
    const CLOSED      = 'closed';

    protected $status;

    public function __construct($status)
    {
        $this->status = $status;
    }

    public function toArray()
    {
        return [
            'status' => $this->status
        ];
    }

    public function __toString()
    {
        return $this->status;
    }
}
