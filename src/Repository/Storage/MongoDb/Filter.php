<?php

// db.ticket.find({tags: {$elemMatch: { $eq: 'Moo'}}}, {_id: 1, tags: 1}).pretty();
// 
namespace Consolidate\Ticket\Repository\Storage\MongoDb;

use Consolidate\Ticket\Data\Role;

use \MongoCollection;
use \MongoId;
use \Exception;

class Filter // extends BaseFilter ?
{
    protected $filter;

    protected function _stack($field, $filter) {
        // If nothing is set, then no worries
        if (!isset($this->filter[$field])) {
            $this->filter[$field] = $filter;
            return $this;
        }

        // Otherwise we must merge the filters on that field
    }

    public function contains($field, $value) {
        return $this->_stack($field, ['$elemMatch' => ['$eq' => $value]]);
    }

    public function hasTag($tag) {
        return $this->contains('tag', $value);
    }

    public function hasRole($role) {
        return $this->_stack('roles.' . $role, ['$exists' => 1]);
    }

    // public function assignedTo(Participant $participant)
    // {
    //     return $this->contains('roles.' . Role::ASSIGNED, (string)$participant);
    // }

    public function equals($field, $value) {
        return $this->_stack($field, $value);
    }

    public function text($field, $value) {
        return $this->_stack($field, ['$text' => ['$search' => $value]]);
    }

    public function toArray() {
        return $this->filter;
    }
}