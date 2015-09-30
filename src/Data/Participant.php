<?php

namespace Consolidate\Ticket\Data;

/**
 * Glorified parameterbag
 *
 * @todo Extract parameterbag stuff
 */
class Participant implements Data
{
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

    public function toArray()
    {
        return $this->param + ['label' => $this->label];
    }

    public static function fromArray(array $data)
    {
        $class = new self($data['label']);
        unset($data['label']);
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $class->set($key, $value);
            }
        }
        return $class;
    }

    public function __toString()
    {
        return (string)$this->getLabel();
    }
}
