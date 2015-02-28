<?php

namespace Consolidate\Ticket\Data;

/**
 * Glorified parameterbag
 *
 * @todo Extract parameterbag stuff
 */
class Participant implements Data
{
    use Resolvable;

    protected $label;
    protected $param = array();

    public function __construct($label)
    {
        $this->setLabel($label);
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function set($key, $value)
    {
        $this->param[$key] = $value;
    }

    public function get($key)
    {
        return $this->param[$key];
    }

    public function fromArray(array $values)
    {
        $this->param = $values;
    }

    public function toArray()
    {
        return $this->param;
    }

    public function __toString()
    {
        return $this->label;
    }
}
