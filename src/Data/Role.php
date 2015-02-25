<?php

namespace Consolidate\Ticket\Data;

class Role implements Data {

    const WORKER = 'worker';
    const OBSERVER = 'observer';
    const CREATOR = 'creator';

    protected $role;
    protected $participant;

    public function __construct(Participant $participant, $role) {
        $this->participant = $participant;
        $this->role = $role;
    }

    public function getRole() {
        return $this->role;
    }

    public function getParticipant() {
        return $this->participant;
    }

    public function __toString() {
        return (string)$this->getRole() . " on " . (string)$this->getParticipant();
    }
}