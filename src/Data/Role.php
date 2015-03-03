<?php

namespace Consolidate\Ticket\Data;

use Consolidate\Ticket\Data\Participant;

class Role implements Data
{
    /**
     * Worker designates that a Participant has worked on a ticket
     */
    const WORKER = 'worker';

    /**
     * Assigned indicates who at a particular time point had this item assigned to them
     */
    const ASSIGNED = 'assigned';

    /**
     * Observer indicates who wants to know about updates to this ticket
     */
    const OBSERVER = 'observer';

    /**
     * Creator indicates the original creator of this ticket
     */
    const CREATOR = 'creator';

    protected $role;
    protected $participant;

    public function __construct(Participant $participant, $role)
    {
        $this->participant = $participant;
        $this->role = $role;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getParticipant()
    {
        return $this->participant;
    }

    public function toArray()
    {
        return [
            'role'        => $this->role,
            'participant' => $this->participant->toArray()
        ];
    }

    public static function fromArray(array $data)
    {
        return new self(Participant::fromArray($data['participant']), $data['role']);
    }

    public function __toString()
    {
        return (string)$this->getRole() . " on " . (string)$this->getParticipant();
    }
}
